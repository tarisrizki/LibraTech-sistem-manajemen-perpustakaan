<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    private static array $titles = [
        ['Bumi', 'Tere Liye'], ['Laskar Pelangi', 'Andrea Hirata'], ['Ayat-Ayat Cinta', 'Habiburrahman El Shirazy'],
        ['Perahu Kertas', 'Dee Lestari'], ['Cantik Itu Luka', 'Eka Kurniawan'], ['Negeri 5 Menara', 'Ahmad Fuadi'],
        ['Dilan 1990', 'Pidi Baiq'], ['Anak Semua Bangsa', 'Pramoedya Ananta Toer'],
        ['Bumi Manusia', 'Pramoedya Ananta Toer'], ['Sang Pemimpi', 'Andrea Hirata'],
        ['Hujan', 'Tere Liye'], ['Pulang', 'Tere Liye'], ['Rindu', 'Tere Liye'],
        ['Gadis Kretek', 'Ratih Kumala'], ['Lelaki Harimau', 'Eka Kurniawan'],
        ['Laut Bercerita', 'Leila S. Chudori'], ['Pulang', 'Leila S. Chudori'],
        ['Supernova', 'Dee Lestari'], ['Aroma Karsa', 'Dee Lestari'],
        ['Das Kapital (Terjemahan)', 'Karl Marx'], ['Sapiens (Terjemahan)', 'Yuval Noah Harari'],
        ['Atomic Habits (Terjemahan)', 'James Clear'], ['Clean Code (Terjemahan)', 'Robert C. Martin'],
        ['Pemrograman Berorientasi Objek', 'Nurul Huda'], ['Sistem Basis Data', 'Fathansyah'],
        ['Jaringan Komputer Modern', 'Andrew S. Tanenbaum'], ['Kecerdasan Buatan', 'Suyanto'],
        ['Algoritma dan Struktur Data', 'Rinaldi Munir'], ['Rekayasa Perangkat Lunak', 'Roger S. Pressman'],
        ['Fisika Dasar', 'Halliday & Resnick'], ['Biologi Sel', 'Campbell'],
        ['Sejarah Indonesia Modern', 'M.C. Ricklefs'], ['Nusantara: Sejarah Indonesia', 'Bernard H.M. Vlekke'],
        ['Biografi Soekarno', 'Cindy Adams'], ['Habibie & Ainun', 'Bacharuddin Jusuf Habibie'],
        ['B.J. Habibie: Biografi', 'Makmur Makka'], ['Kartini: Biografi', 'Sitisoemandari Soeroto'],
        ['Filsafat Ilmu', 'Jujun S. Suriasumantri'], ['Pengantar Sosiologi', 'Soerjono Soekanto'],
        ['Ekonomi Mikro', 'Mankiw'], ['Manajemen Strategis', 'Fred R. David'],
        ['Negeri Para Bedebah', 'Tere Liye'], ['Orang-Orang Bloomington', 'Budi Darma'],
        ['Tetralogi Buru: Jejak Langkah', 'Pramoedya Ananta Toer'], ['Salah Asuhan', 'Abdoel Moeis'],
    ];

    public function definition(): array
    {
        [$title, $author] = fake()->randomElement(self::$titles);

        return [
            'category_id' => Category::factory(),
            'title' => $title.' '.fake()->unique()->numerify('(#-###)'),
            'author' => $author,
            'isbn' => fake()->unique()->isbn13(),
            'stock' => fake()->numberBetween(0, 10),
            'description' => fake('id_ID')->paragraph(3),
            'cover_path' => null,
            'published_year' => fake()->numberBetween(1980, 2025),
        ];
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $a): array => ['stock' => 0]);
    }

    public function inStock(): static
    {
        return $this->state(fn (array $a): array => ['stock' => fake()->numberBetween(1, 10)]);
    }
}
