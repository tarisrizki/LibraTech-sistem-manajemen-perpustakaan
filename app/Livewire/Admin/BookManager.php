<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BookManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public ?int $editingId = null;

    public string $title = '';

    public string $author = '';

    public string $isbn = '';

    public string $category_id = '';

    public string $stock = '1';

    public ?string $description = null;

    public ?string $published_year = null;

    public $cover = null;

    public ?string $existingCover = null;

    public bool $showForm = false;

    public string $filterPill = 'semua';

    public function mount(): void
    {
        Gate::authorize('viewAny', Book::class);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function setFilterPill(string $value): void
    {
        $this->filterPill = $value;
        if ($value === 'semua') {
            $this->categoryFilter = '';
        } elseif ($value === 'habis') {
            $this->categoryFilter = 'habis';
        } else {
            $this->categoryFilter = $value;
        }
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Book::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $book = Book::findOrFail($id);
        Gate::authorize('update', $book);

        $this->editingId = $book->id;
        $this->title = $book->title;
        $this->author = $book->author;
        $this->isbn = $book->isbn;
        $this->category_id = (string) $book->category_id;
        $this->stock = (string) $book->stock;
        $this->description = $book->description;
        $this->published_year = $book->published_year ? (string) $book->published_year : null;
        $this->existingCover = $book->cover_path;
        $this->cover = null;
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function save(): void
    {
        $isEditing = $this->editingId !== null;
        $book = $isEditing ? Book::findOrFail($this->editingId) : new Book;

        if ($isEditing) {
            Gate::authorize('update', $book);
        } else {
            Gate::authorize('create', Book::class);
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'.($isEditing ? ','.$this->editingId : '')],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        $this->validate($rules);

        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'category_id' => $this->category_id,
            'stock' => (int) $this->stock,
            'description' => $this->description,
            'published_year' => $this->published_year ? (int) $this->published_year : null,
        ];

        if ($this->cover) {
            if ($isEditing && $book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $data['cover_path'] = $this->cover->store('covers', 'public');
        }

        if ($isEditing) {
            $book->update($data);
            session()->flash('success', 'Buku berhasil diperbarui.');
        } else {
            Book::create($data);
            session()->flash('success', 'Buku berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $book = Book::findOrFail($id);
        Gate::authorize('delete', $book);
        $book->delete();
        session()->flash('success', 'Buku berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'title', 'author', 'isbn', 'category_id', 'stock', 'description', 'published_year', 'cover', 'existingCover']);
        $this->stock = '1';
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Book::with('category')->latest();

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', "%{$term}%")
                    ->orWhere('author', 'like', "%{$term}%")
                    ->orWhere('isbn', 'like', "%{$term}%");
            });
        }

        if ($this->categoryFilter !== '') {
            if ($this->categoryFilter === 'habis') {
                $query->where('stock', '<=', 0);
            } elseif (is_numeric($this->categoryFilter)) {
                $query->where('category_id', $this->categoryFilter);
            }
        }

        $books = $query->paginate(10);

        return view('livewire.admin.book-manager', [
            'books' => $books,
            'categories' => $this->categories(),
        ]);
    }
}
