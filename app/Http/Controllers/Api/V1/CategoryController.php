<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    public function index(CategoryService $categoryService)
    {
        $categories = $categoryService->getCategories();

        return new CategoryCollection($categories);
    }

    public function show(CategoryService $categoryService, $id)
    {
        try {
            $category = $categoryService->getCategory($id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $storeCategoryRequest, CategoryService $categoryService)
    {
        try {
            $category = $categoryService->createFromRequest($storeCategoryRequest);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseCreated(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $updateCategoryRequest, CategoryService $categoryService, $id)
    {
        try {
            $category = $categoryService->updateFromRequest($updateCategoryRequest, $id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseOk(new CategoryResource($category));
    }

    public function destroy(CategoryService $categoryService, $id)
    {
        try {
            $categoryService->delete($id);
        } catch (\Exception $e) {
            abort($e->getCode(), $e->getMessage());
        }

        return $this->responseDeleted();
    }
}
