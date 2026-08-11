<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('isbn', 20)->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('cover_path')->nullable();
            $table->smallInteger('published_year')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['title', 'author']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
