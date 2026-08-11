<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::query()->with('category');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            });
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

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('catalog.index', compact('books', 'categories'));
    }

    public function show(Book $book): View
    {
        $book->load('category');

        return view('catalog.show', compact('book'));
    }
}
