<?php

declare(strict_types=1);

use App\Models\Book;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

it('invalidates books cache when a book is saved', function (): void {
    if (config('cache.default') !== 'redis' && env('CACHE_STORE') !== 'redis') {
        $this->markTestSkipped('only runs with CACHE_STORE=redis (see POIN B step 5)');
    }

    $book = Book::factory()->create();

    Cache::put('books:test-invalidation', 'old', 900);
    expect(Cache::get('books:test-invalidation'))->toBe('old');

    $book->update(['title' => $book->title.' x']);

    expect(Cache::get('books:test-invalidation'))->toBeNull();
});

it('invalidates categories cache when a category is saved', function (): void {
    if (config('cache.default') !== 'redis' && env('CACHE_STORE') !== 'redis') {
        $this->markTestSkipped('only runs with CACHE_STORE=redis');
    }

    $category = Category::factory()->create();

    Cache::put('categories:all', 'old-cat', 900);
    expect(Cache::get('categories:all'))->toBe('old-cat');

    $category->update(['name' => $category->name.' x']);

    expect(Cache::get('categories:all'))->toBeNull();
});
