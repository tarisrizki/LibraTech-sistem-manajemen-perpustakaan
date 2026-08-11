<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\LoanStatus;
use App\Models\Loan;
use Illuminate\Console\Command;

class MarkOverdueLoans extends Command
{
    protected $signature = 'loans:mark-overdue';

    protected $description = 'Mark approved loans past due date as overdue';

    public function handle(): int
    {
        $count = Loan::where('status', LoanStatus::Approved)
            ->where('due_at', '<', now())
            ->update(['status' => LoanStatus::Overdue]);

        $this->info("Marked {$count} loan(s) as overdue.");

        return self::SUCCESS;
    }
}
