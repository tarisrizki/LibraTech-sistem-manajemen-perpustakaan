<?php

declare(strict_types=1);

use App\Enums\LoanStatus;
use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;

it('allows member to request a loan when stock is available', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 3]);
    $member = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($member)->post(route('loans.store'), ['book_id' => $book->id]);

    $response->assertRedirect(route('loans.index'));
    expect(Loan::where('user_id', $member->id)->where('book_id', $book->id)->exists())->toBeTrue();
});

it('rejects loan request when stock is zero', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 0]);
    $member = User::factory()->create();

    $response = $this->actingAs($member)->post(route('loans.store'), ['book_id' => $book->id]);

    $response->assertRedirect();
    expect(Loan::where('user_id', $member->id)->exists())->toBeFalse();
});

it('prevents duplicate active loan for same book', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 5]);
    $member = User::factory()->create();
    Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Pending]);

    $response = $this->actingAs($member)->post(route('loans.store'), ['book_id' => $book->id]);

    $response->assertRedirect();
    expect(Loan::where('user_id', $member->id)->where('book_id', $book->id)->count())->toBe(1);
});

it('limits member to 3 active approved loans', function (): void {
    $category = Category::factory()->create();
    $books = Book::factory()->count(4)->create(['category_id' => $category->id, 'stock' => 5]);
    $member = User::factory()->create();
    foreach ($books->take(3) as $book) {
        Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Approved]);
    }

    $response = $this->actingAs($member)->post(route('loans.store'), ['book_id' => $books->last()->id]);

    $response->assertRedirect();
    expect(Loan::where('user_id', $member->id)->where('book_id', $books->last()->id)->exists())->toBeFalse();
});

it('admin can approve pending loan and stock decreases', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 2]);
    $member = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $loan = Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Pending]);

    $this->actingAs($admin)->post(route('admin.loans.approve', $loan));

    expect($book->refresh()->stock)->toBe(1);
    expect($loan->refresh()->status)->toBe(LoanStatus::Approved);
    expect($loan->refresh()->due_at)->not->toBeNull();
});

it('admin can reject pending loan with reason', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 2]);
    $member = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $loan = Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Pending]);

    $this->actingAs($admin)->post(route('admin.loans.reject', $loan), ['rejection_reason' => 'Buku rusak']);

    expect($loan->refresh()->status)->toBe(LoanStatus::Rejected);
    expect($loan->refresh()->rejection_reason)->toBe('Buku rusak');
});

it('admin can mark approved loan as returned and stock increases', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 1]);
    $member = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin']);
    $loan = Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Approved, 'due_at' => now()->addDays(7)]);

    $this->actingAs($admin)->post(route('admin.loans.return', $loan));

    expect($book->refresh()->stock)->toBe(2);
    expect($loan->refresh()->status)->toBe(LoanStatus::Returned);
});

it('mark-overdue command flags overdue loans', function (): void {
    $category = Category::factory()->create();
    $book = Book::factory()->create(['category_id' => $category->id, 'stock' => 1]);
    $member = User::factory()->create();
    $loan = Loan::factory()->create(['user_id' => $member->id, 'book_id' => $book->id, 'status' => LoanStatus::Approved, 'due_at' => now()->subDay()]);

    $this->artisan('loans:mark-overdue')->assertExitCode(0);

    expect($loan->refresh()->status)->toBe(LoanStatus::Overdue);
});

it('guest cannot access admin routes', function (): void {
    $this->get(route('admin.books.index'))->assertRedirect(route('login'));
});

it('member cannot access admin routes', function (): void {
    $member = User::factory()->create();
    $this->actingAs($member)->get(route('admin.books.index'))->assertForbidden();
});

it('catalog is publicly accessible', function (): void {
    $this->get(route('catalog.index'))->assertOk();
});

it('api returns paginated books', function (): void {
    $category = Category::factory()->create();
    Book::factory()->count(5)->create(['category_id' => $category->id]);
    $response = $this->getJson('/api/v1/books');
    $response->assertOk()->assertJsonStructure(['data', 'links', 'meta']);
});
