<?php

namespace App\Http\Resources\Author;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            // load callback returned [value] if pivot relationship data exist in modelObject
            'isAutographed' => $this->whenPivotLoaded('author_book', function () {
                return $this->pivot->is_autographed;
            }),
        ];
    }
}
