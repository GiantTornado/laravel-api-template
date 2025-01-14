<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CategoryService
{
    public function getCategories()
    {
        $categories = Category::all();

        return $categories;
    }

    public function getCategory($id)
    {
        return Cache::remember("category#$id", 3600, function () use ($id) {
            $category = Category::find($id);

            if (!$category) {
                throw new \Exception('Category not found.', 404);
            }

            return $category;
        });
    }

    public function createFromRequest($request)
    {
        if (Gate::denies('create', Category::class)) {
            throw new \Exception('Not Authorized.', 403);
        }

        $category = Category::create([
            'name' => $request->name,
        ]);

        return $category->refresh();
    }

    public function updateFromRequest($request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new \Exception('category not found.', 422);
        }

        if (Gate::inspect('update', [$category])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        $category->update([
            'name' => $request->name,
        ]);

        $updatedCategory = $category->refresh();

        Cache::put("category#$id", $updatedCategory, 3600);

        return $updatedCategory;
    }

    public function delete($id)
    {
        $category = Category::find($id);

        if (!$category) {
            throw new \Exception('category not found.', 422);
        }

        if (Gate::inspect('delete', [$category])->denied()) {
            throw new \Exception('Not Authorized.', 403);
        }

        if ($this->hasBooks($category->id)) {
            throw new \Exception("Category cannot be deleted because it has associated books.", 422);
        }

        Cache::forget("category#$id");

        $category->delete();
    }

    protected function hasBooks($categoryId)
    {
        return Book::where('category_id', $categoryId)->exists();
    }
}
