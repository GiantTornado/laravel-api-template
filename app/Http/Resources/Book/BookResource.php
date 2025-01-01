<?php

namespace App\Http\Resources\Book;

use App\Http\Resources\Author\AuthorCollection;
use App\Http\Resources\Category\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->whenHas('description'),
            'publishedAt' => $this->whenHas('published_at'),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'authors' => new AuthorCollection($this->whenLoaded('authors')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
