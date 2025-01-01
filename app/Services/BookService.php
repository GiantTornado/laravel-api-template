<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Filters\Book\BookFilters;
use App\Models\Book;
use App\Notifications\BookCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class BookService
{
    public function getBooks($filters, $pageSize = null)
    {
        $queryBuilder = Book::with(['category', 'authors'])
            ->select('*')
            //apply [local scope]
            ->newlyCreated(Carbon::now()->subYear());

        $filteredBooks = app(BookFilters::class)->filter([
            'queryBuilder' => $queryBuilder,
            'params' => $filters
        ]);

        $books = $filteredBooks->when($pageSize, fn($query) => $query->paginate($pageSize), fn($query) => $query->get());

        return $books;
    }

    public function getBook($id)
    {
        $book = Book::find($id);
        if (!$book) {
            throw new \Exception('Book Not Found.', 404);
        }

        return $book;
    }

    public function createFromRequest($request)
    {
        if (Gate::denies('create', Book::class)) {
            throw new \Exception('Not Authorized.', 403);
        }
        $book = null;
        DB::transaction(function () use ($request, &$book) {
            $book = Book::create([
                'title' => $request->title,
                'slug' => str()->slug($request->title, '-'),
                'description' => $request->description,
                'published_at' => $request->publishedAt,
                'category_id' => $request->categoryId,
            ]);

            $book->authors()->attach($request->authorIds);
        });

        if (is_null($book)) {
            throw new \Exception('Failed to create the book.');
        }

        //re-retreive [model] from the database with all its relations and update [modelObject] with it
        return $book->refresh()->load(['category', 'authors']);
    }

    public function updateFromRequest($request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            throw new \Exception('Book not found.', 422);
        }

        if (Gate::inspect('update', [$book])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        DB::transaction(function () use ($request, $book) {
            $book->update([
                'title' => $request->title,
                'slug' => str()->slug($request->title, '-'),
                'description' => $request->description,
                'published_at' => $request->publishedAt,
                'category_id' => $request->categoryId,
            ]);

            $book->authors()->sync($request->authorIds);
        });

        return $book->refresh()->load(['category', 'authors']);
    }

    public function delete($id)
    {
        $book = Book::find($id);

        if (!$book) {
            throw new \Exception('Book not found.', 422);
        }

        if (Gate::inspect('delete', [$book])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        DB::transaction(function () use ($book) {
            $book->authors()->detach();
            $book->delete();
        });
    }

    public function sendBookCreatedNotification($book, $recepients)
    {
        Notification::send($recepients, new BookCreatedNotification($book));
    }
}
