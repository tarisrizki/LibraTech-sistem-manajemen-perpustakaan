<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'status' => LoanStatus::Pending,
            'requested_at' => now(),
            'approved_at' => null,
            'due_at' => null,
            'returned_at' => null,
            'rejection_reason' => null,
            'approved_by' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => LoanStatus::Approved,
            'approved_at' => now()->subDays(fake()->numberBetween(1, 5)),
            'due_at' => now()->addDays(7),
            'approved_by' => User::factory()->admin(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => LoanStatus::Rejected,
            'rejection_reason' => fake('id_ID')->sentence(6),
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => LoanStatus::Returned,
            'approved_at' => now()->subDays(10),
            'due_at' => now()->subDays(3),
            'returned_at' => now()->subDays(1),
            'approved_by' => User::factory()->admin(),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => LoanStatus::Overdue,
            'approved_at' => now()->subDays(14),
            'due_at' => now()->subDays(2),
            'approved_by' => User::factory()->admin(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => LoanStatus::Pending,
            'requested_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }
}
