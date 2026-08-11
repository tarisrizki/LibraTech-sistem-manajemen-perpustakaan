<?php

declare(strict_types=1);

namespace App\Livewire\Catalog;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'category_id')]
    public string $categoryId = '';

    #[Url(as: 'available')]
    public bool $available = false;

    #[Url(as: 'sort')]
    public string $sort = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatingAvailable(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'categoryId', 'available', 'sort']);
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $query = Book::query()->with('category');

        if (trim($this->search) !== '') {
            $query->search(trim($this->search));
        }
        if ($this->categoryId !== '' && ctype_digit($this->categoryId)) {
            $query->where('category_id', (int) $this->categoryId);
        }
        if ($this->available) {
            $query->available();
        }

        match ($this->sort) {
            'title' => $query->orderBy('title'),
            'popular' => $query->withCount('loans')->orderByDesc('loans_count'),
            default => $query->latest(),
        };

        $books = $query->paginate(12);
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $featured = Book::with('category')->latest()->limit(2)->get();
        $newArrivals = Book::with('category')->latest()->limit(4)->get();

        return view('livewire.catalog.index', compact('books', 'categories', 'featured', 'newArrivals'));
    }
}
