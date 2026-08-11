<?php

declare(strict_types=1);

namespace App\Enums;

enum LoanStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Returned = 'returned';
    case Overdue = 'overdue';

    public function isActive(): bool
    {
        return $this === self::Pending || $this === self::Approved || $this === self::Overdue;
    }
}
