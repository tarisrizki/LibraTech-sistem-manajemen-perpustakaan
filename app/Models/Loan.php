<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanStatus;
use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property LoanStatus $status
 * @property Carbon|null $requested_at
 * @property Carbon|null $approved_at
 * @property Carbon|null $due_at
 * @property Carbon|null $returned_at
 */
class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'requested_at',
        'approved_at',
        'due_at',
        'returned_at',
        'rejection_reason',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => LoanStatus::class,
            'requested_at' => 'datetime',
            'approved_at' => 'datetime',
            'due_at' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Book, $this> */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /** @return BelongsTo<User, $this> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    #[Scope]
    protected function pending(Builder $query): void
    {
        $query->where('status', LoanStatus::Pending);
    }

    #[Scope]
    protected function approved(Builder $query): void
    {
        $query->where('status', LoanStatus::Approved);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereIn('status', [LoanStatus::Pending, LoanStatus::Approved, LoanStatus::Overdue]);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
