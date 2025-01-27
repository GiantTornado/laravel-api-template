<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Exceptions\Book\BookNotFoundException;
use App\Http\Resources\Book\BookCollection;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookControllerTest extends TestCase {
    // execute each test within a database transaction and rollback after each test
    use RefreshDatabase;

    private $admin;

    protected function setUp(): void {
        parent::setUp();
        $this->admin = $this->createRandomUser();
    }

    public function test_can_get_all_books(): void {
        Sanctum::actingAs($this->admin);
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
        Sanctum::actingAs($this->admin);
        Book::factory()->for(Category::factory())->has(Author::factory())->count(config('meta.pagination.page_size.books') + 1)->create();

        $response = $this->getJson(route('books.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(config('meta.pagination.page_size.books'), 'data');
        $response->assertJsonPath('meta.last_page', 2);
    }

    public function test_can_filter_books_by_title() {
        Sanctum::actingAs($this->admin);
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
        // test whether the response contains given data anywhere.
        $response->assertJsonFragment(['id' => $aliceBook->id]);
        $response->assertJsonMissing(['id' => $bobBook->id]);
    }

    public function test_book_price_is_shown_correctly() {
        Sanctum::actingAs($this->admin);
        $book = $this->createRandomBook(['price' => 123.45]);

        $response = $this->getJson(route('books.show', [
            'id' => $book->id,
        ]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['price' => '123.45']);
    }

    public function test_can_books_be_sorted_by_price() {
        Sanctum::actingAs($this->admin);
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
        Sanctum::actingAs($this->admin);
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

    public function test_viewer_user_can_not_create_book() {
        $viewer = User::factory()->has(Profile::factory())->viewer()->create();
        Sanctum::actingAs($viewer);
        $book_data = Book::factory()->make()->toArray();

        $response = $this->postJson(route('books.store', $book_data));

        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $this->assertDatabaseMissing(Book::class, $book_data);
    }

    public function test_can_delete_a_book() {
        Sanctum::actingAs($this->admin);
        $book = Book::factory()->has(Author::factory()->count(3))->create();

        $response = $this->deleteJson(route('books.destroy', ['id' => $book->id]));

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted($book);
        foreach ($book->authors as $author) {
            $this->assertDatabaseMissing('author_book', [
                'book_id' => $book->id,
                'author_id' => $author->id,
            ]);
        }
    }

    public function test_exception_is_thrown_for_unavailable_book() {
        Sanctum::actingAs($this->admin);

        $this->withoutExceptionHandling();

        $this->expectException(BookNotFoundException::class);

        $this->getJson(route('books.show', ['id' => 1]));
    }
}
