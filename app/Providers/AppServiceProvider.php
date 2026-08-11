<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Observers\BookObserver;
use App\Observers\CategoryObserver;
use App\Policies\BookPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\LoanPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Loan::class, LoanPolicy::class);

        Book::observe(BookObserver::class);
        Category::observe(CategoryObserver::class);
    }
}
