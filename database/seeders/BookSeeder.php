<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $map = Category::pluck('id', 'slug');

        $books = [
            ['title' => 'Bumi', 'author' => 'Tere Liye', 'isbn' => '9786020332956', 'cat' => 'fiksi', 'stock' => 5, 'year' => 2014],
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'isbn' => '9789793062792', 'cat' => 'fiksi', 'stock' => 3, 'year' => 2005],
            ['title' => 'Perahu Kertas', 'author' => 'Dee Lestari', 'isbn' => '9789791227780', 'cat' => 'fiksi', 'stock' => 4, 'year' => 2009],
            ['title' => 'Cantik Itu Luka', 'author' => 'Eka Kurniawan', 'isbn' => '9786020336244', 'cat' => 'fiksi', 'stock' => 2, 'year' => 2002],
            ['title' => 'Dilan: Dia Adalah Dilanku Tahun 1990', 'author' => 'Pidi Baiq', 'isbn' => '9786027870413', 'cat' => 'fiksi', 'stock' => 0, 'year' => 2014],
            ['title' => 'Gadis Kretek', 'author' => 'Ratih Kumala', 'isbn' => '9786020333830', 'cat' => 'fiksi', 'stock' => 6, 'year' => 2012],
            ['title' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'isbn' => '9786024246945', 'cat' => 'fiksi', 'stock' => 3, 'year' => 2017],
            ['title' => 'Negeri 5 Menara', 'author' => 'Ahmad Fuadi', 'isbn' => '9789790622386', 'cat' => 'fiksi', 'stock' => 5, 'year' => 2009],
            ['title' => 'Ayat-Ayat Cinta', 'author' => 'Habiburrahman El Shirazy', 'isbn' => '9789791365104', 'cat' => 'fiksi', 'stock' => 2, 'year' => 2004],
            ['title' => 'Sang Pemimpi', 'author' => 'Andrea Hirata', 'isbn' => '9789793062112', 'cat' => 'fiksi', 'stock' => 0, 'year' => 2006],

            ['title' => 'Sapiens: Riwayat Umat Manusia', 'author' => 'Yuval Noah Harari', 'isbn' => '9786024242987', 'cat' => 'non-fiksi', 'stock' => 4, 'year' => 2011],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'isbn' => '9786020652786', 'cat' => 'non-fiksi', 'stock' => 7, 'year' => 2018],
            ['title' => 'Filosofi Teras', 'author' => 'Henry Manampiring', 'isbn' => '9786020651239', 'cat' => 'non-fiksi', 'stock' => 5, 'year' => 2018],
            ['title' => 'Sebuah Seni untuk Bersikap Bodo Amat', 'author' => 'Mark Manson', 'isbn' => '9786023853449', 'cat' => 'non-fiksi', 'stock' => 3, 'year' => 2016],
            ['title' => 'Berani Tidak Disukai', 'author' => 'Ichiro Kishimi & Fumitake Koga', 'isbn' => '9786020333212', 'cat' => 'non-fiksi', 'stock' => 0, 'year' => 2013],
            ['title' => 'The Psychology of Money', 'author' => 'Morgan Housel', 'isbn' => '9786024815879', 'cat' => 'non-fiksi', 'stock' => 4, 'year' => 2020],
            ['title' => 'Mindset: Psikologi Sukses', 'author' => 'Carol Dweck', 'isbn' => '9786020734441', 'cat' => 'non-fiksi', 'stock' => 2, 'year' => 2006],

            ['title' => 'Clean Code', 'author' => 'Robert C. Martin', 'isbn' => '9780132350884', 'cat' => 'teknologi', 'stock' => 5, 'year' => 2008],
            ['title' => 'Pemrograman Berorientasi Objek dengan Java', 'author' => 'Nurul Huda', 'isbn' => '9786024347761', 'cat' => 'teknologi', 'stock' => 3, 'year' => 2019],
            ['title' => 'Sistem Basis Data', 'author' => 'Fathansyah', 'isbn' => '9786022411516', 'cat' => 'teknologi', 'stock' => 2, 'year' => 2015],
            ['title' => 'Jaringan Komputer', 'author' => 'Andrew S. Tanenbaum', 'isbn' => '9780132126953', 'cat' => 'teknologi', 'stock' => 0, 'year' => 2010],
            ['title' => 'Kecerdasan Buatan: Konsep dan Penerapan', 'author' => 'Suyanto', 'isbn' => '9786028758611', 'cat' => 'teknologi', 'stock' => 4, 'year' => 2017],
            ['title' => 'Algoritma dan Pemrograman', 'author' => 'Rinaldi Munir', 'isbn' => '9786024434123', 'cat' => 'teknologi', 'stock' => 6, 'year' => 2018],
            ['title' => 'Rekayasa Perangkat Lunak', 'author' => 'Roger S. Pressman', 'isbn' => '9780078022128', 'cat' => 'teknologi', 'stock' => 3, 'year' => 2014],

            ['title' => 'Fisika Dasar Jilid 1', 'author' => 'Halliday & Resnick', 'isbn' => '9780470469088', 'cat' => 'sains', 'stock' => 3, 'year' => 2013],
            ['title' => 'Biologi Sel dan Molekuler', 'author' => 'Campbell & Reece', 'isbn' => '9780321558237', 'cat' => 'sains', 'stock' => 2, 'year' => 2017],
            ['title' => 'Sejarah Sains Dunia', 'author' => 'John Gribbin', 'isbn' => '9780140297416', 'cat' => 'sains', 'stock' => 0, 'year' => 2002],
            ['title' => 'Cosmos', 'author' => 'Carl Sagan', 'isbn' => '9780345539437', 'cat' => 'sains', 'stock' => 4, 'year' => 1980],
            ['title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'isbn' => '9780553380163', 'cat' => 'sains', 'stock' => 5, 'year' => 1988],
            ['title' => 'The Selfish Gene', 'author' => 'Richard Dawkins', 'isbn' => '9780192860927', 'cat' => 'sains', 'stock' => 2, 'year' => 1976],

            ['title' => 'Sejarah Indonesia Modern', 'author' => 'M.C. Ricklefs', 'isbn' => '9789793783797', 'cat' => 'sejarah', 'stock' => 4, 'year' => 2008],
            ['title' => 'Nusantara: Sejarah Indonesia', 'author' => 'Bernard H.M. Vlekke', 'isbn' => '9789794134148', 'cat' => 'sejarah', 'stock' => 2, 'year' => 2008],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya Ananta Toer', 'isbn' => '9789799731234', 'cat' => 'sejarah', 'stock' => 5, 'year' => 1980],
            ['title' => 'Anak Semua Bangsa', 'author' => 'Pramoedya Ananta Toer', 'isbn' => '9789799731241', 'cat' => 'sejarah', 'stock' => 0, 'year' => 1980],
            ['title' => 'Perang Dunia II: Sejarah Lengkap', 'author' => 'Antony Beevor', 'isbn' => '9780670021228', 'cat' => 'sejarah', 'stock' => 3, 'year' => 2012],
            ['title' => 'Api Sejarah Jilid 1', 'author' => 'Ahmad Mansur Suryanegara', 'isbn' => '9786022900234', 'cat' => 'sejarah', 'stock' => 3, 'year' => 2014],

            ['title' => 'Habibie & Ainun', 'author' => 'Bacharuddin Jusuf Habibie', 'isbn' => '9786028570840', 'cat' => 'biografi', 'stock' => 4, 'year' => 2010],
            ['title' => 'Soekarno: Biografi', 'author' => 'Cindy Adams', 'isbn' => '9789794137408', 'cat' => 'biografi', 'stock' => 3, 'year' => 1966],
            ['title' => 'Kartini: Sebuah Biografi', 'author' => 'Sitisoemandari Soeroto', 'isbn' => '9789794285124', 'cat' => 'biografi', 'stock' => 2, 'year' => 2004],
            ['title' => 'Steve Jobs', 'author' => 'Walter Isaacson', 'isbn' => '9781451648539', 'cat' => 'biografi', 'stock' => 5, 'year' => 2011],
            ['title' => 'Becoming', 'author' => 'Michelle Obama', 'isbn' => '9781524763138', 'cat' => 'biografi', 'stock' => 0, 'year' => 2018],
            ['title' => 'Einstein: His Life and Universe', 'author' => 'Walter Isaacson', 'isbn' => '9780743264747', 'cat' => 'biografi', 'stock' => 3, 'year' => 2007],
        ];

        foreach ($books as $b) {
            $catId = $map[$b['cat']] ?? null;
            if (! $catId) {
                continue;
            }
            // ponytail: cover_path null => Blade pakai Open Library per ISBN (real cover, no hardcode); isi storage kalau ada upload
            Book::firstOrCreate(
                ['isbn' => $b['isbn']],
                [
                    'category_id' => $catId,
                    'title' => $b['title'],
                    'author' => $b['author'],
                    'stock' => $b['stock'],
                    'description' => match ($b['isbn']) {
                        '9786020332956' => 'Raib, Seli, Ali berpetualang melintasi klan dan dimensi. Tere Liye meramu fiksi petualangan remaja dengan worldbuilding rapi.',
                        '9789793062792' => '10 anak Belitung melawan keterbatasan lewat sekolah kayu. Andrea Hirata menulis tentang mimpi, guru, dan keteguhan.',
                        '9786024246945' => 'Kisah aktivis 1998 dan keluarga yang menunggu jawaban. Leila Chudori menautkan sejarah dan trauma dengan bahasa jernih.',
                        '9786020652786' => 'Perubahan kecil yang konsisten mengalahkan motivasi besar sesaat. James Clear memberi sistem kebiasaan yang praktis.',
                        '9786024242987' => 'Dari sapiens pemburu ke jaringan global. Harari merangkum evolusi sosial manusia dengan argumen tajam.',
                        '9780132350884' => 'Prinsip menulis kode yang mudah dibaca dan dirawat. Rujukan wajib untuk refactoring dan craftsmanship.',
                        '9780553380163' => 'Hawking menjelaskan asal semesta dengan analogi ringan. Pengantar kosmologi untuk pembaca umum.',
                        '9780345539437' => 'Carl Sagan menuntun dari atom ke galaksi. Sains sebagai cara melihat, bukan sekadar fakta.',
                        '9781451648539' => 'Biografi intens tentang obsesi produk dan kepemimpinan. Isaacson merunut sisi terang dan gelap Jobs.',
                        '9781524763138' => 'Dari South Side Chicago ke Gedung Putih. Michelle Obama menulis tentang identitas, kerja, dan keluarga.',
                        default => $b['title'].' karya '.$b['author'].' terbit '.$b['year'].'. Koleksi LibraTech untuk kategori '.$b['cat'].'.',
                    },
                    'published_year' => $b['year'],
                ]
            );
        }
    }
}
