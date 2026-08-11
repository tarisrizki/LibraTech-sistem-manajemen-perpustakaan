<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Fiksi', 'slug' => 'fiksi'],
            ['name' => 'Non-Fiksi', 'slug' => 'non-fiksi'],
            ['name' => 'Teknologi', 'slug' => 'teknologi'],
            ['name' => 'Sains', 'slug' => 'sains'],
            ['name' => 'Sejarah', 'slug' => 'sejarah'],
            ['name' => 'Biografi', 'slug' => 'biografi'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
