<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class CoverImage extends Component
{
    public function __construct(
        public ?string $coverWebpUrl = null,
        public ?string $coverPath = null,
        public ?string $isbn = null,
        public ?int $coverId = null,
        public string $alt = '',
        public string $class = '',
        public string $sizes = '(max-width:768px) 100vw, 25vw',
        public bool $eager = false,
    ) {}

    public function render(): View
    {
        return view('components.cover-image');
    }
}
