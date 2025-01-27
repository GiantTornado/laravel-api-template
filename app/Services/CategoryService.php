<?php

namespace App\Services;

use App\Exceptions\Category\CategoryNotFoundException;
use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Gate;

class CategoryService {
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository) {
        $this->categoryRepository = $categoryRepository;
    }

    public function getCategories() {
        return $this->categoryRepository->findAll();
    }

    public function getCategory(int $id) {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException;
        }

        return $category;
    }

    public function createFromRequest($request) {
        Gate::authorize('create', Category::class);

        return $this->categoryRepository->create([
            'name' => $request->name,
        ]);
    }

    public function updateFromRequest($request, string $id) {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException;
        }

        Gate::authorize('update', $category);
 
        return $this->categoryRepository->update($category, [
            'name' => $request->name,
        ]);
    }

    public function delete(string $id) {
        $category = $this->categoryRepository->findById($id);

        if (!$category) {
            throw new CategoryNotFoundException;
        }

        Gate::authorize('delete', $category);

        if ($this->categoryRepository->hasBooks($id)) {
            throw new \Exception('Category cannot be deleted because it has associated books.', 422);
        }

        $this->categoryRepository->delete($category);
    }
}
