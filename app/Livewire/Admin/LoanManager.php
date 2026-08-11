<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Actions\Loans\ApproveLoanAction;
use App\Actions\Loans\RejectLoanAction;
use App\Actions\Loans\ReturnLoanAction;
use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class LoanManager extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public string $search = '';

    public ?int $rejectId = null;

    public string $rejectionReason = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Loan::class);
        $this->statusFilter = request()->string('status')->toString();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function approve(int $id, ApproveLoanAction $action): void
    {
        Gate::authorize('approve', Loan::class);
        $loan = Loan::findOrFail($id);
        try {
            $action->execute($loan, auth()->user());
            session()->flash('success', 'Peminjaman disetujui.');
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openReject(int $id): void
    {
        Gate::authorize('reject', Loan::class);
        $this->rejectId = $id;
        $this->rejectionReason = '';
    }

    public function cancelReject(): void
    {
        $this->rejectId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(RejectLoanAction $action): void
    {
        Gate::authorize('reject', Loan::class);
        $this->validate(['rejectionReason' => ['required', 'string', 'max:255']]);
        $loan = Loan::findOrFail($this->rejectId);
        try {
            $action->execute($loan, $this->rejectionReason, auth()->user());
            session()->flash('success', 'Peminjaman ditolak.');
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
        $this->rejectId = null;
        $this->rejectionReason = '';
    }

    public function markReturned(int $id, ReturnLoanAction $action): void
    {
        Gate::authorize('return', Loan::class);
        $loan = Loan::findOrFail($id);
        try {
            $action->execute($loan);
            session()->flash('success', 'Buku ditandai telah dikembalikan.');
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(): View
    {
        $query = Loan::with(['user', 'book.category'])->latest('requested_at');

        if ($this->statusFilter !== '') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->search !== '') {
            $term = $this->search;
            $query->where(function ($q) use ($term): void {
                $q->whereHas('book', fn ($qb) => $qb->where('title', 'like', "%{$term}%"))
                    ->orWhereHas('user', fn ($qb) => $qb->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"));
            });
        }

        $loans = $query->paginate(15);

        $statuses = [
            '' => 'Semua status',
            LoanStatus::Pending->value => 'Pending',
            LoanStatus::Approved->value => 'Approved',
            LoanStatus::Rejected->value => 'Rejected',
            LoanStatus::Returned->value => 'Returned',
            LoanStatus::Overdue->value => 'Overdue',
        ];

        return view('livewire.admin.loan-manager', [
            'loans' => $loans,
            'statuses' => $statuses,
        ]);
    }
}
