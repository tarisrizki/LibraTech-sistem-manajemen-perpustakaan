<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Loans\RequestLoanAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use Dedoc\Scramble\Attributes\Group;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group('Loans')]
class LoanController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $loans = Loan::where('user_id', $request->user()->id)->with('book.category')->latest('requested_at')->paginate(15);

        return LoanResource::collection($loans);
    }

    public function store(Request $request, RequestLoanAction $action): JsonResponse
    {
        $validated = $request->validate(['book_id' => ['required', 'integer', 'exists:books,id']]);

        try {
            $loan = $action->execute($request->user(), (int) $validated['book_id']);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => ['book_id' => [$e->getMessage()]]], 422);
        }

        $loan->load('book.category');

        return (new LoanResource($loan))->response()->setStatusCode(201);
    }
}
