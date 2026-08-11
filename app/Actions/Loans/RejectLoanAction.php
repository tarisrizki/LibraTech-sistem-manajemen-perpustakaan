<?php

declare(strict_types=1);

namespace App\Actions\Loans;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\Log;

class RejectLoanAction
{
    public function execute(Loan $loan, string $reason, User $admin): Loan
    {
        if ($loan->status !== LoanStatus::Pending) {
            throw new DomainException('Hanya peminjaman berstatus pending yang dapat ditolak.');
        }

        $loan->update([
            'status' => LoanStatus::Rejected,
            'rejection_reason' => $reason,
            'approved_by' => $admin->id,
        ]);

        Log::channel('daily')->info('Loan rejected', [
            'loan_id' => $loan->id,
            'reason' => $reason,
            'rejected_by' => $admin->id,
        ]);

        return $loan->refresh();
    }
}
