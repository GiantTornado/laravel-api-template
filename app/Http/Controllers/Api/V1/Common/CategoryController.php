<?php

namespace App\Http\Controllers\Api\V1\Common;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Services\CategoryService;

class CategoryController extends Controller {
    public function index(CategoryService $categoryService) {
        $categories = $categoryService->getCategories();

        return new CategoryCollection($categories);
    }

    public function show(CategoryService $categoryService, $id) {
        $category = $categoryService->getCategory($id);

        return new CategoryResource($category);
    }

    public function store(StoreCategoryRequest $storeCategoryRequest, CategoryService $categoryService) {
        $category = $categoryService->createFromRequest($storeCategoryRequest);

        return $this->responseCreated(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $updateCategoryRequest, CategoryService $categoryService, $id) {
        $category = $categoryService->updateFromRequest($updateCategoryRequest, $id);

        return $this->responseOk(new CategoryResource($category));
    }

    public function destroy(CategoryService $categoryService, $id) {
        $categoryService->delete($id);

        return $this->responseDeleted();
    }
}
