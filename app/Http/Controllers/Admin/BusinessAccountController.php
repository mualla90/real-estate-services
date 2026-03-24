<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessAccount\RejectRequest;
use App\Models\BusinessAccount;
use App\Services\BusinessAccount\BusinessAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BusinessAccountController extends Controller
{
    public function __construct(
        protected BusinessAccountService $service
    ) {}

    public function index(Request $request): View
    {
        $businessAccounts = BusinessAccount::query()
            ->with(['user', 'city', 'activityType'])
            ->latest()
            ->paginate(15);

        return view('admin.business-accounts.index', compact('businessAccounts'));
    }

    public function show(BusinessAccount $businessAccount): View
    {
        $businessAccount->load(['user', 'city', 'activityType', 'reviewedByAdmin']);

        return view('admin.business-accounts.show', compact('businessAccount'));
    }

    public function approve(BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->approve($businessAccount, auth('admin')->user());

        return redirect()
            ->back()
            ->with('success', 'Business account approved successfully.');
    }

    public function reject(RejectRequest $request, BusinessAccount $businessAccount): RedirectResponse
    {
        $this->service->reject(
            $businessAccount,
            auth('admin')->user(),
            $request->rejection_reason
        );

        return redirect()
            ->back()
            ->with('success', 'Business account rejected successfully.');
    }
}
