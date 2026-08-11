<?php

declare(strict_types=1);

namespace App\Actions\Loans;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use DomainException;

class RequestLoanAction
{
    public function execute(User $user, int $bookId): Loan
    {
        $book = Book::findOrFail($bookId);

        if ($book->stock <= 0) {
            throw new DomainException('Buku tidak tersedia (stok habis).');
        }

        $hasActiveLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', [LoanStatus::Pending, LoanStatus::Approved, LoanStatus::Overdue])
            ->exists();

        if ($hasActiveLoan) {
            throw new DomainException('Anda sudah memiliki peminjaman aktif untuk buku ini.');
        }

        $activeCount = Loan::where('user_id', $user->id)
            ->whereIn('status', [LoanStatus::Approved, LoanStatus::Overdue])
            ->count();

        if ($activeCount >= 3) {
            throw new DomainException('Batas maksimal 3 peminjaman aktif tercapai.');
        }

        return Loan::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'status' => LoanStatus::Pending,
            'requested_at' => now(),
        ]);
    }
}
