<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\GetBookRequest;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookCollection;
use App\Http\Resources\Book\BookResource;
use App\Services\BookService;

class BookController extends Controller {
    public function index(GetBookRequest $getBookRequest, BookService $bookService) {
        $books = $bookService->getBooks(
            filters: $getBookRequest->all(),
            pageSize: request()->page_size ?? config('meta.pagination.page_size.books')
        );

        return new BookCollection($books);
    }

    public function show(BookService $bookService, $id) {
        $book = $bookService->getBook($id);

        return new BookResource($book);
    }

    public function store(StoreBookRequest $storeBookRequest, BookService $bookService) {
        $book = $bookService->createFromRequest($storeBookRequest);
        $bookService->sendBookCreatedNotification($book, auth()->user());

        return $this->responseCreated(new BookResource($book));
    }

    public function update(UpdateBookRequest $updateBookRequest, BookService $bookService, $id) {
        $book = $bookService->updateFromRequest($updateBookRequest, $id);

        return $this->responseOk(new BookResource($book));
    }

    public function destroy(BookService $bookService, $id) {
        $bookService->delete($id);

        return $this->responseDeleted();
    }
}
