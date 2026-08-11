<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CategoryManager extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public bool $showForm = false;

    public function mount(): void
    {
        Gate::authorize('viewAny', Category::class);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        Gate::authorize('create', Category::class);
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $category = Category::findOrFail($id);
        Gate::authorize('update', $category);
        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
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
        $category = $isEditing ? Category::findOrFail($this->editingId) : new Category;

        if ($isEditing) {
            Gate::authorize('update', $category);
        } else {
            Gate::authorize('create', Category::class);
        }

        $this->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'.($isEditing ? ','.$this->editingId : '')],
            'slug' => ['nullable', 'string', 'max:120', 'unique:categories,slug'.($isEditing ? ','.$this->editingId : '')],
        ]);

        $slug = $this->slug !== '' ? Str::slug($this->slug) : Str::slug($this->name);

        if ($isEditing) {
            $category->update(['name' => $this->name, 'slug' => $slug]);
            session()->flash('success', 'Kategori berhasil diperbarui.');
        } else {
            Category::create(['name' => $this->name, 'slug' => $slug]);
            session()->flash('success', 'Kategori berhasil ditambahkan.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $category = Category::findOrFail($id);
        Gate::authorize('delete', $category);

        if ($category->books()->exists()) {
            session()->flash('error', 'Tidak dapat menghapus kategori yang masih memiliki buku.');

            return;
        }

        $category->delete();
        session()->flash('success', 'Kategori berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'slug']);
        $this->resetValidation();
    }

    public function render(): View
    {
        $query = Category::withCount('books')->latest();

        if ($this->search !== '') {
            $query->where('name', 'like', "%{$this->search}%");
        }

        $categories = $query->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories,
        ]);
    }
}
