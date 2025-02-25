<?php

namespace App\Repositories\Interfaces;

use App\Models\Category;

interface CategoryRepositoryInterface {
    public function findAll();

    public function findById(string $id, array $relations = []);

    public function create(array $data);

    public function update(Category $category, array $data);

    public function delete(Category $category);

    public function hasBooks(string $categoryId);
}
