<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorCollection;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),
            'published_at' => $this->whenHas('published_at'),
            'price' => $this->whenHas('price', fn () => number_format($this->price, 2)),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'authors' => new AuthorCollection($this->whenLoaded('authors')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
