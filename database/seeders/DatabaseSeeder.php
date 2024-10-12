<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Call Seeders
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        if (app()->environment(['local', 'staging'])) {
            //HasOne Relationship
            User::factory()->viewer()->has(Profile::factory())->count(1)->create();

            $author_1 = Author::factory()->create();
            $author_2 = Author::factory()->create();

            //HasMany Relationship
            $category_1 = Category::factory()->has(Book::factory()->count(3))->create();
            $category_2 = Category::factory()->has(Book::factory()->count(2))->create();

            $author_1->books()->attach($category_1->books);
            $author_1->books()->attach($category_2->books);
            $author_2->books()->attach($category_2->books);

            Book::factory()
                //Implicitly create function that returns the given array
                ->state(['title' => 'Test Book'])
                //BelongsTo Relationship
                ->for(Category::factory())
                //BelongsToMany Relationship
                ->has(Author::factory())
                ->create();

            Book::factory()
                //BelongsTo Relationship with Model Instance
                ->for($category_1)
                //BelongsToMany Relationship with Setting Pivot Table Columns
                ->hasAttached(Author::factory(), ['is_autographed' => true])
                ->create();
        }
    }
}
