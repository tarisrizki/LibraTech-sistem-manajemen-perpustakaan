<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

#[Group('Admin — Books')]
class AdminBookController extends Controller
{
    public function store(StoreBookRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['cover_path'] = $this->handleCover($request);
        unset($validated['cover']);

        $book = Book::create($validated);
        $book->load('category');

        return (new BookResource($book))->response()->setStatusCode(201);
    }

    public function update(UpdateBookRequest $request, Book $book): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $validated['cover_path'] = $this->handleCover($request);
        }

        unset($validated['cover']);
        $book->update($validated);
        $book->load('category');

        return (new BookResource($book))->response();
    }

    public function destroy(Book $book): JsonResponse
    {
        Gate::authorize('delete', $book);
        $book->delete();

        return response()->json(null, 204);
    }

    private function handleCover(StoreBookRequest|UpdateBookRequest $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        return $request->file('cover')->store('covers', 'public');
    }
}
