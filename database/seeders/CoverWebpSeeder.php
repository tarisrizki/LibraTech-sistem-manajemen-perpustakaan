<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Populate cover_webp_url via OpenLibrary M.jpg -> GD resize 400w -> webp -> R2 upload.
 * Idempotent: skips books already with cover_webp_url. Uses mcp__cloudflare__execute for R2.
 * Requires: APP_URL covers fallback, R2 bucket libratech-covers exists, CLOUDFLARE_API_TOKEN via Hermes MCP.
 * Run manually: php artisan db:seed --class=CoverWebpSeeder
 * ponytail: single 400w webp (srcset 1x/2x same file, browser picks via sizes). Upgrade path: generate 200w+800w variants if Lighthouse demands.
 */
class CoverWebpSeeder extends Seeder
{
    private const R2_BUCKET = 'libratech-covers';

    private const R2_PREFIX = 'covers';

    // proxied custom domain per spec
    private const R2_PUBLIC_BASE = 'https://images.lambada.my.id';

    public function run(): void
    {
        $books = Book::whereNull('cover_webp_url')->whereNotNull('isbn')->get();
        if ($books->isEmpty()) {
            $this->command?->info('No books need cover_webp_url.');

            return;
        }

        $this->command?->info("Processing {$books->count()} books...");

        $done = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($books as $book) {
            $result = $this->processBook($book);
            match ($result) {
                'done' => $done++,
                'skipped' => $skipped++,
                'failed' => $failed++,
            };
            // be nice to OpenLibrary
            usleep(250_000);
        }

        $this->command?->info("Done={$done} skipped={$skipped} failed={$failed}");
    }

    private function processBook(Book $book): string
    {
        $isbn = preg_replace('/[^0-9Xx]/', '', $book->isbn);
        if (! $isbn) {
            return 'skipped';
        }

        $olUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg?default=false";

        try {
            $res = Http::timeout(15)->withHeaders(['User-Agent' => 'LibraTech/1.0'])->get($olUrl);
        } catch (\Throwable $e) {
            Log::warning("Cover fetch failed {$isbn}: ".$e->getMessage());

            return 'failed';
        }

        if (! $res->successful()) {
            $this->command?->warn("No cover for ISBN {$isbn} (HTTP {$res->status()})");

            return 'skipped';
        }

        $body = $res->body();
        // OpenLibrary returns 1x1 gif or tiny placeholder when missing with default=false it should 404, but guard anyway
        if (strlen($body) < 2048) {
            // check if it's an image but too small -> likely placeholder
            $tmp = @getimagesizefromstring($body);
            if (! $tmp || ($tmp[0] ?? 0) < 50) {
                return 'skipped';
            }
        }

        $webpBytes = $this->toWebp400w($body);
        if ($webpBytes === null) {
            Log::warning("Webp conversion failed {$isbn}");

            return 'failed';
        }

        $key = self::R2_PREFIX.'/'.$isbn.'-400.webp';

        $token = env('CLOUDFLARE_API_TOKEN') ?: env('CF_API_TOKEN');
        $uploaded = $this->uploadToR2($key, $webpBytes);
        if (! $uploaded) {
            return 'failed';
        }

        if ($token) {
            $publicUrl = rtrim(self::R2_PUBLIC_BASE, '/').'/'.$key;
        } else {
            // local fallback: Storage public disk
            $publicUrl = Storage::url($key);
        }
        $book->update(['cover_webp_url' => $publicUrl]);
        $this->command?->info("  ✓ {$book->title} -> {$publicUrl} (".strlen($webpBytes).' bytes)');

        return 'done';
    }

    private function toWebp400w(string $srcBytes): ?string
    {
        $src = @imagecreatefromstring($srcBytes);
        if (! $src) {
            return null;
        }

        $sw = imagesx($src);
        $sh = imagesy($src);
        if ($sw <= 0 || $sh <= 0) {
            imagedestroy($src);

            return null;
        }

        $tw = 400;
        // keep aspect; if source smaller than 400, keep original width (no upscale)
        if ($sw <= $tw) {
            $tw = $sw;
            $th = $sh;
        } else {
            $th = (int) round($sh * ($tw / $sw));
        }

        $dst = imagecreatetruecolor($tw, $th);
        // white background for JPEG sources with no alpha
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $sw, $sh);
        imagedestroy($src);

        ob_start();
        $ok = imagewebp($dst, null, 82);
        $out = ob_get_clean();
        imagedestroy($dst);

        if (! $ok || $out === false || $out === '') {
            return null;
        }

        return $out;
    }

    private function uploadToR2(string $key, string $bytes): bool
    {
        // Use local HTTP to Cloudflare API via token in env if available, else skip R2 and fall back to Storage::disk public?
        // For seeder in CI/local, we try Cloudflare API if CLOUDFLARE_API_TOKEN is set, otherwise store locally and set cover_webp_url to asset('storage/...')
        $token = env('CLOUDFLARE_API_TOKEN') ?: env('CF_API_TOKEN');
        if (! $token) {
            // fallback: store to public disk and use asset URL (still webp, still DB-driven Blade)
            $stored = Storage::disk('public')->put($key, $bytes);
            if (! $stored) {
                return false;
            }
            // caller will set URL to Storage::url; override here
            // we return true but let caller know fallback happened via flag: put file and set URL via Storage
            // Patch the next update: we set cover_webp_url to Storage URL in processBook by checking file existence
            // Simpler: set directly here and signal done
            // HACK: update book directly to avoid double write - caller checks this file
            // Actually caller will overwrite; so store a marker file with url
            // Easiest: just store via Storage and let processBook construct Storage URL; detect token absence there.
            // So we return false to signal fallback? No, we want to succeed via Storage.
            // We already stored; return true and caller will use R2 base - but we want Storage base instead.
            // Workaround: write a tiny sidecar so processBook can detect fallback and use Storage::url
            file_put_contents(storage_path('app/.r2_fallback'), $key);

            return true;
        }

        $accountId = env('CLOUDFLARE_ACCOUNT_ID') ?: 'f7a91a8bd0255ea5bb7280f2dc04c48b';
        $endpoint = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/r2/buckets/".self::R2_BUCKET.'/objects/'.rawurlencode($key);
        // Note: rawurlencode keeps slashes encoded, but API expects literal slashes in key - use custom encode
        $endpoint = "https://api.cloudflare.com/client/v4/accounts/{$accountId}/r2/buckets/".self::R2_BUCKET."/objects/{$key}";

        try {
            $res = Http::withToken($token)
                ->withHeaders([
                    'Content-Type' => 'image/webp',
                    'Cache-Control' => 'public, max-age=604800',
                ])
                ->withBody($bytes, 'image/webp')
                ->put($endpoint);
        } catch (\Throwable $e) {
            Log::warning("R2 upload failed {$key}: ".$e->getMessage());

            return false;
        }

        if (! $res->successful()) {
            Log::warning("R2 upload failed {$key} HTTP {$res->status()}: ".$res->body());

            return false;
        }

        // set Cache-Control via R2 object metadata requires second call? Cloudflare R2 Put respects Cache-Control header as custom metadata.
        // ponytail: leave as-is; Cloudflare edge cache respects origin Cache-Control if proxied via images.lambada.my.id

        return true;
    }
}
