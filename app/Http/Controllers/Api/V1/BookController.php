<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Http\Resources\Book\BookCollection;
use App\Http\Resources\Book\BookResource;
use App\Services\BookService;

class BookController extends Controller
{
    public function index(BookService $bookService)
    {
        $books = $bookService->getBooks(
            filters: request()->all(),
            pageSize: request()->pageSize ?? config('meta.pagination.page_size')
        );

        return new BookCollection($books);
    }

    public function show(BookService $bookService, $id)
    {
        try {
            $book = $bookService->getBook($id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return new BookResource($book);
    }

    public function store(StoreBookRequest $storeBookRequest, BookService $bookService)
    {
        try {
            $book = $bookService->createFromRequest($storeBookRequest);
            $bookService->sendBookCreatedNotification($book, auth()->user());
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseCreated(new BookResource($book));
    }

    public function update(UpdateBookRequest $updateBookRequest, BookService $bookService, $id)
    {
        try {
            $book = $bookService->updateFromRequest($updateBookRequest, $id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseOk(new BookResource($book));
    }

    public function destroy(BookService $bookService, $id)
    {
        try {
            $bookService->delete($id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseDeleted();
    }
}
