<?php

declare(strict_types=1);

namespace App\Livewire\Catalog;

use App\Enums\LoanStatus;
use App\Models\Book;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Show extends Component
{
    public Book $book;

    public function mount(Book $book): void
    {
        $this->book = $book->loadMissing(['category']);
    }

    #[Layout('layouts.app')]
    public function render(): View
    {
        $related = Book::with('category')
            ->where('category_id', $this->book->category_id)
            ->where('id', '!=', $this->book->id)
            ->latest()->limit(4)->get();

        $activeLoans = $this->book->loans()->whereIn('status', [LoanStatus::Approved, LoanStatus::Overdue])->count();

        return view('livewire.catalog.show', [
            'related' => $related,
            'activeLoans' => $activeLoans,
        ]);
    }
}
