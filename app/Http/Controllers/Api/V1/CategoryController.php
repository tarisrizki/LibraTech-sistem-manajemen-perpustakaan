<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

#[Group('Categories')]
class CategoryController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // ponytail: single-key cache 900s; use tags if per-filter keys needed later
        $categories = Cache::remember('categories:api:index', 900, fn () => Category::orderBy('name')->get());

        return CategoryResource::collection($categories);
    }
}
