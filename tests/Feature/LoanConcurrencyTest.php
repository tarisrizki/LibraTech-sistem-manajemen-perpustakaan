<?php

declare(strict_types=1);

use App\Actions\Loans\ApproveLoanAction;
use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;

it('prevents stock going negative when two pending loans for same book with stock=1 are approved sequentially', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 1]);
    $member1 = User::factory()->create();
    $member2 = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $loan1 = Loan::factory()->create(['user_id' => $member1->id, 'book_id' => $book->id, 'status' => LoanStatus::Pending]);
    $loan2 = Loan::factory()->create(['user_id' => $member2->id, 'book_id' => $book->id, 'status' => LoanStatus::Pending]);

    $action = app(ApproveLoanAction::class);

    $action->execute($loan1, $admin);
    expect($book->refresh()->stock)->toBe(0);
    expect($loan1->refresh()->status)->toBe(LoanStatus::Approved);

    expect(fn () => $action->execute($loan2, $admin))->toThrow(DomainException::class, 'Stok buku habis');

    expect($book->refresh()->stock)->toBe(0);
    expect($loan2->refresh()->status)->toBe(LoanStatus::Pending);
});

it('approve uses transaction and lockForUpdate', function (): void {
    $code = file_get_contents(app_path('Actions/Loans/ApproveLoanAction.php'));
    expect($code)->toContain('lockForUpdate');
    expect($code)->toContain('DB::transaction');

    $codeReturn = file_get_contents(app_path('Actions/Loans/ReturnLoanAction.php'));
    expect($codeReturn)->toContain('lockForUpdate');
});
