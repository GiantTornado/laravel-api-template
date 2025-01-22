<?php

namespace App\Services;

use App\Exceptions\Book\BookNotFoundException;
use App\Exceptions\Book\DailyBookPublishLimitExceededException;
use App\Filters\Book\BookFilters;
use App\Models\Book;
use App\Notifications\BookCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class BookService {
    public function getBooks($filters, $pageSize = null) {
        $queryBuilder = Book::with(['category', 'authors'])
            ->select('*')
            // apply [local scope]
            ->newlyCreated(Carbon::now()->subYear());

        $filteredBooks = app(BookFilters::class)->filter([
            'queryBuilder' => $queryBuilder,
            'params' => $filters,
        ]);

        $books = $filteredBooks->when($pageSize, fn ($query) => $query->paginate($pageSize), fn ($query) => $query->get());

        return $books;
    }

    public function getBook($id) {
        $book = Book::with(['category'])->find($id);
        if (!$book) {
            throw new BookNotFoundException;
        }

        return $book;
    }

    public function createFromRequest($request) {
        if (Gate::denies('create', Book::class)) {
            throw new \Exception('Not Authorized.', 403);
        }

        $booksPublishedTodayCount = $this->getBooksPublishedTodayCount();
        if ($booksPublishedTodayCount > config('modules.book.max_books_allowed_to_publish_per_day')) {
            throw new DailyBookPublishLimitExceededException;
        }

        $book = DB::transaction(function () use ($request, &$book) {
            $book = Book::create([
                'title' => $request->title,
                'description' => $request->description,
                'published_at' => $request->publishedAt,
                'price' => $request->price,
                'category_id' => $request->categoryId,
            ]);

            $book->authors()->attach($request->authorIds);

            return $book;
        });

        // re-retreive [model] from the database with all its relations and update [modelObject] with it
        return $book->refresh()->load(['category', 'authors']);
    }

    public function updateFromRequest($request, $id) {
        $book = Book::find($id);

        if (!$book) {
            throw new BookNotFoundException;
        }

        if (Gate::inspect('update', [$book])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        DB::transaction(function () use ($request, $book) {
            $book->update([
                'title' => $request->title,
                'description' => $request->description,
                'published_at' => $request->publishedAt,
                'price' => $request->price,
                'category_id' => $request->categoryId,
            ]);

            $book->authors()->sync($request->authorIds);
        });

        return $book->refresh()->load(['category', 'authors']);
    }

    public function delete($id) {
        $book = Book::find($id);

        if (!$book) {
            throw new BookNotFoundException;
        }

        if (Gate::inspect('delete', [$book])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        DB::transaction(function () use ($book) {
            $book->authors()->detach();
            $book->delete();
        });
    }

    public function sendBookCreatedNotification($book, $recepients) {
        Notification::send($recepients, new BookCreatedNotification($book));
    }

    public function getBooksPublishedTodayCount(): int {
        return Book::whereDate('published_at', now()->toDateString())->count();
    }
}
