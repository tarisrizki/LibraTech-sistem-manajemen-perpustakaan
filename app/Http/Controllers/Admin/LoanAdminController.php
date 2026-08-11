<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Loans\ApproveLoanAction;
use App\Actions\Loans\RejectLoanAction;
use App\Actions\Loans\ReturnLoanAction;
use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LoanAdminController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Loan::class);

        $query = Loan::with(['user', 'book.category'])->latest('requested_at');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $loans = $query->paginate(15)->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function approve(Loan $loan, ApproveLoanAction $action): RedirectResponse
    {
        Gate::authorize('approve', Loan::class);
        $action->execute($loan, auth()->user());

        return back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject(Request $request, Loan $loan, RejectLoanAction $action): RedirectResponse
    {
        Gate::authorize('reject', Loan::class);

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:255'],
        ]);

        $action->execute($loan, $validated['rejection_reason'], auth()->user());

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function return(Loan $loan, ReturnLoanAction $action): RedirectResponse
    {
        Gate::authorize('return', Loan::class);
        $action->execute($loan);

        return back()->with('success', 'Buku ditandai telah dikembalikan.');
    }
}
