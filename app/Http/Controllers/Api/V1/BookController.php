<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

#[Group('Books')]
class BookController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // ponytail: Cache::remember 900 per fullUrl; upgrade to tags when granular invalidation needed
        $key = 'books:api:index:'.md5($request->fullUrl());

        $paginator = Cache::remember($key, 900, function () use ($request) {
            $query = Book::query()->with('category');
            if ($search = $request->string('search')->toString()) {
                $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('author', 'like', "%{$search}%"));
            }
            if ($categoryId = $request->input('category_id')) {
                $query->where('category_id', $categoryId);
            }
            if ($request->boolean('available')) {
                $query->where('stock', '>', 0);
            }
            $sort = $request->string('sort')->toString();
            match ($sort) {
                'title' => $query->orderBy('title'),
                'popular' => $query->withCount('loans')->orderByDesc('loans_count'),
                default => $query->latest(),
            };

            return $query->paginate(12)->withQueryString();
        });

        return BookResource::collection($paginator);
    }

    public function show(Book $book): BookResource
    {
        $book->load('category');

        return new BookResource($book);
    }
}
