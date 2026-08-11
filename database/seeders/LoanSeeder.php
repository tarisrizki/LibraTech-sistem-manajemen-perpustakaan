<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $members = User::where('role', 'member')->get();
        $admin = User::where('role', 'admin')->first();
        $books = Book::all();

        if ($members->isEmpty() || $books->isEmpty()) {
            return;
        }

        $adminId = $admin?->id;

        $statuses = [
            LoanStatus::Pending,
            LoanStatus::Approved,
            LoanStatus::Rejected,
            LoanStatus::Returned,
            LoanStatus::Overdue,
        ];

        $used = [];

        foreach ($members as $member) {
            $memberBooks = $books->random(min(4, $books->count()));
            foreach ($memberBooks as $idx => $book) {
                $key = $member->id.'-'.$book->id;
                if (isset($used[$key])) {
                    continue;
                }
                $used[$key] = true;

                $status = $statuses[array_rand($statuses)];

                $data = [
                    'user_id' => $member->id,
                    'book_id' => $book->id,
                    'status' => $status,
                    'requested_at' => now()->subDays(rand(1, 20)),
                ];

                match ($status) {
                    LoanStatus::Pending => $data += [
                        'approved_at' => null, 'due_at' => null, 'returned_at' => null,
                        'rejection_reason' => null, 'approved_by' => null,
                    ],
                    LoanStatus::Approved => $data += [
                        'approved_at' => now()->subDays(rand(1, 5)),
                        'due_at' => now()->addDays(7),
                        'approved_by' => $adminId,
                    ],
                    LoanStatus::Rejected => $data += [
                        'rejection_reason' => 'Ditolak: cek syarat peminjaman.',
                        'approved_by' => $adminId,
                    ],
                    LoanStatus::Returned => $data += [
                        'approved_at' => now()->subDays(14),
                        'due_at' => now()->subDays(7),
                        'returned_at' => now()->subDays(rand(1, 5)),
                        'approved_by' => $adminId,
                    ],
                    LoanStatus::Overdue => $data += [
                        'approved_at' => now()->subDays(14),
                        'due_at' => now()->subDays(rand(1, 5)),
                        'approved_by' => $adminId,
                    ],
                };

                Loan::create($data);
            }
        }
    }
}
