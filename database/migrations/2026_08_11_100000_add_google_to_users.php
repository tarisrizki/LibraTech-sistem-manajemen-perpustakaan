<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'google_avatar')) {
                $table->string('google_avatar', 500)->nullable()->after('google_id');
            }
            // make password nullable for OAuth-only accounts
            // sqlite/pgsql compatible — use raw check
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropColumn('google_id');
            }
            if (Schema::hasColumn('users', 'google_avatar')) {
                $table->dropColumn('google_avatar');
            }
        });
    }
};
