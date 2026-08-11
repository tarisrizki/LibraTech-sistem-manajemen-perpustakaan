<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property bool $is_available
 */
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'stock',
        'description',
        'cover_path',
        'cover_webp_url',
        'published_year',
    ];

    protected function casts(): array
    {
        return [
            'stock' => 'integer',
            'published_year' => 'integer',
        ];
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<Loan, $this> */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    protected function isAvailable(): Attribute
    {
        return Attribute::get(fn (): bool => $this->stock > 0);
    }

    #[Scope]
    protected function available(Builder $query): void
    {
        $query->where('stock', '>', 0);
    }

    #[Scope]
    protected function search(Builder $query, string $term): void
    {
        $query->where(function (Builder $q) use ($term): void {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('author', 'like', "%{$term}%");
        });
    }
}
