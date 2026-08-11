<?php

declare(strict_types=1);

namespace App\Actions\Loans;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApproveLoanAction
{
    public function execute(Loan $loan, User $admin): Loan
    {
        if ($loan->status !== LoanStatus::Pending) {
            throw new DomainException('Hanya peminjaman berstatus pending yang dapat disetujui.');
        }

        return DB::transaction(function () use ($loan, $admin): Loan {
            $book = Book::whereKey($loan->book_id)->lockForUpdate()->firstOrFail();

            if ($book->stock <= 0) {
                throw new DomainException('Stok buku habis, tidak dapat menyetujui peminjaman.');
            }

            $book->decrement('stock');

            $loan->update([
                'status' => LoanStatus::Approved,
                'approved_at' => now(),
                'due_at' => now()->addDays(7),
                'approved_by' => $admin->id,
            ]);

            Log::channel('daily')->info('Loan approved', [
                'loan_id' => $loan->id,
                'book_id' => $loan->book_id,
                'user_id' => $loan->user_id,
                'approved_by' => $admin->id,
            ]);

            return $loan->refresh();
        });
    }
}
