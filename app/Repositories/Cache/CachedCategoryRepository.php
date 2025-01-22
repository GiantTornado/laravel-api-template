<?php

namespace App\Repositories\Cache;

use App\Helpers\CacheHelper;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class CachedCategoryRepository implements CategoryRepositoryInterface {
    protected $repository;

    protected $cacheTTL = 3600;

    public function __construct(CategoryRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function findAll() {
        return CacheHelper::cacheWithFallback('categories.all', 3600, fn () => $this->repository->findAll());
    }

    public function findById(int $id, array $relations = []) {
        return CacheHelper::cacheWithFallback("category#$id", 3600, fn () => $this->repository->findById($id));
    }

    public function create(array $data) {
        $category = $this->repository->create($data);
        Cache::forget('categories.all');

        return $category;
    }

    public function update(Category $category, array $data) {
        $updatedCategory = $this->repository->update($category, $data);
        Cache::put("category#{$category->id}", $updatedCategory, $this->cacheTTL);
        Cache::forget('categories.all');

        return $updatedCategory;
    }

    public function delete(Category $category) {
        $this->repository->delete($category);
        Cache::forget("category#{$category->id}");
        Cache::forget('categories.all');
    }

    public function hasBooks(int $categoryId) {
        return $this->repository->hasBooks($categoryId);
    }
}
