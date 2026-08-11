<?php

declare(strict_types=1);

namespace App\Actions\Loans;

use App\Enums\LoanStatus;
use App\Models\Loan;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnLoanAction
{
    public function execute(Loan $loan): Loan
    {
        if (! in_array($loan->status, [LoanStatus::Approved, LoanStatus::Overdue], true)) {
            throw new DomainException('Hanya peminjaman berstatus approved/overdue yang dapat dikembalikan.');
        }

        return DB::transaction(function () use ($loan): Loan {
            $book = $loan->book()->lockForUpdate()->firstOrFail();
            $book->increment('stock');

            $loan->update([
                'status' => LoanStatus::Returned,
                'returned_at' => now(),
            ]);

            Log::channel('daily')->info('Loan returned', [
                'loan_id' => $loan->id,
                'book_id' => $loan->book_id,
                'user_id' => $loan->user_id,
            ]);

            return $loan->refresh();
        });
    }
}
