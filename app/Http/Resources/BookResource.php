<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Book */
class BookResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'stock' => $this->stock,
            'is_available' => $this->is_available,
            'description' => $this->description,
            'cover_url' => $this->cover_path ? asset('storage/'.$this->cover_path) : null,
            'published_year' => $this->published_year,
        ];
    }
}
