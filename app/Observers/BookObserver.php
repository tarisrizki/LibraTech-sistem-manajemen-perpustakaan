<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class BookObserver
{
    public function saved(Book $book): void
    {
        $this->flush();
    }

    public function deleted(Book $book): void
    {
        $this->flush();
    }

    private function flush(): void
    {
        Cache::flush();
    }
}
