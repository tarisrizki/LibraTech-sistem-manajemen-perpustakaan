<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Loans\ApproveLoanAction;
use App\Actions\Loans\RejectLoanAction;
use App\Actions\Loans\ReturnLoanAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Loan;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class LoanAdminController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Loan::class);
        $query = Loan::with(['user', 'book.category'])->latest('requested_at');
        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return LoanResource::collection($query->paginate(15)->withQueryString());
    }

    public function approve(Loan $loan, ApproveLoanAction $action): JsonResponse
    {
        Gate::authorize('approve', Loan::class);
        try {
            $result = $action->execute($loan, auth()->user());
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LoanResource($result->load(['user', 'book'])))->response();
    }

    public function reject(Request $request, Loan $loan, RejectLoanAction $action): JsonResponse
    {
        Gate::authorize('reject', Loan::class);
        $validated = $request->validate(['rejection_reason' => ['required', 'string', 'max:255']]);
        try {
            $result = $action->execute($loan, $validated['rejection_reason'], auth()->user());
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LoanResource($result->load(['user', 'book'])))->response();
    }

    public function return(Loan $loan, ReturnLoanAction $action): JsonResponse
    {
        Gate::authorize('return', Loan::class);
        try {
            $result = $action->execute($loan);
        } catch (DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new LoanResource($result->load(['user', 'book'])))->response();
    }
}
