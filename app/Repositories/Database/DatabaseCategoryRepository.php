<?php

namespace App\Repositories\Database;

use App\Models\Book;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class DatabaseCategoryRepository implements CategoryRepositoryInterface {
    public function findAll() {
        return Category::all();
    }

    public function findById(string $id, array $relations = []) {
        return Category::with($relations)->find($id);
    }

    public function create(array $data) {
        return Category::create($data);
    }

    public function update(Category $category, array $data) {
        $category->update($data);

        return $category->refresh();
    }

    public function delete(Category $category) {
        $category->delete();
    }

    public function hasBooks(string $categoryId) {
        return Book::where('category_id', $categoryId)->exists();
    }
}
