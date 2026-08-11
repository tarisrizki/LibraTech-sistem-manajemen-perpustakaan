<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Loans\RequestLoanAction;
use App\Http\Requests\RequestLoanRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(): View
    {
        $loans = auth()->user()->loans()->with('book.category')->latest('requested_at')->paginate(15);

        return view('loans.index', compact('loans'));
    }

    public function store(RequestLoanRequest $request, RequestLoanAction $action): RedirectResponse
    {
        $bookId = (int) $request->validated()['book_id'];

        try {
            $action->execute(auth()->user(), $bookId);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('loans.index')->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }
}
