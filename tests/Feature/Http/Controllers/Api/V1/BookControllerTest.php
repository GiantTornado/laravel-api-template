<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Http\Resources\Book\BookCollection;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase {
    // execute each test within a database transaction and rollback after each test
    use RefreshDatabase;

    private $alice;

    protected function setUp(): void {
        parent::setUp();
        $this->alice = $this->createRandomUser();
    }

    public function test_can_get_all_books(): void {
        Sanctum::actingAs($this->alice);
        Category::factory()->has(Book::factory()->count(3))->create();
        Category::factory()->has(Book::factory()->count(2))->create();
        Book::factory()->state(['title' => 'Test Book'])->for(Category::factory())->has(Author::factory())->create();
        $books = Book::all();

        // add [Accept: application/json] header
        $response = $this->getJson(route('books.index'));

        $response->assertStatus(Response::HTTP_OK)
            // true if value at given [key] is array and its items count are [count]
            ->assertJsonCount($books->count(), 'data')
            // true if given json [subarray] is a subarray of response
            // singular and collection api resources response is automatically wrapped with [data] key
            ->assertJson((new BookCollection($books))->response()->getData(true));
    }

    public function test_can_get_books_paginated() {
        Sanctum::actingAs($this->alice);
        Book::factory()->for(Category::factory())->has(Author::factory())->count(config('meta.pagination.page_size.books') + 1)->create();

        $response = $this->getJson(route('books.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(config('meta.pagination.page_size.books'), 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_can_filter_books_by_title() {
        Sanctum::actingAs($this->alice);
        $aliceBook = $this->createRandomBook(['title' => 'Alice Book', 'description' => null]);
        $bobBook = $this->createRandomBook(['title' => 'Bob Book', 'description' => null]);

        $response = $this->getJson(route('books.index', [
            'searchBy' => $aliceBook->title,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.title', $aliceBook->title);

        $response = $this->getJson(route('books.index', [
            'searchBy' => 'Alice',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $aliceBook->id]);
        $response->assertJsonMissing(['id' => $bobBook->id]);
    }

    public function test_book_price_is_shown_correctly() {
        Sanctum::actingAs($this->alice);
        $book = $this->createRandomBook(['price' => 123.45]);

        $response = $this->getJson(route('books.show', [
            'id' => $book->id,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['price' => '123.45']);
    }

    public function test_can_books_be_sorted_by_price() {
        Sanctum::actingAs($this->alice);
        $cheap = $this->createRandomBook(['price' => 100]);
        $expensiveBook = $this->createRandomBook(['price' => 200]);

        $response = $this->getJson(route('books.index', [
            'sortBy' => 'price',
            'sortOrder' => 'asc',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('data.0.id', $cheap->id);
        $response->assertJsonPath('data.1.id', $expensiveBook->id);

        $response = $this->getJson(route('books.index', [
            'sortBy' => 'price',
            'sortOrder' => 'desc',
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonPath('data.0.id', $expensiveBook->id);
        $response->assertJsonPath('data.1.id', $cheap->id);
    }

    public function test_can_create_a_book() {
        Sanctum::actingAs($this->alice);
        $book_data = Book::factory()->make();
        $book_data['categoryId'] = Category::factory()->create()->id;
        $book_data['authorIds'] = Author::factory()->count(3)->create()->pluck('id')->toArray();

        // add [Accept: application/json] header
        $response = $this->postJson(route('books.store', $book_data->toArray()));

        $response->assertStatus(Response::HTTP_CREATED);
        // assert table has record match the given [array]
        $this->assertDatabaseHas(
            Book::class,
            $book_data->only(['title', 'description']) + ['category_id' => $book_data['categoryId']]
        );
        foreach ($book_data->authorIds as $authorId) {
            $this->assertDatabaseHas('author_book', ['author_id' => $authorId]);
        }
    }
}
