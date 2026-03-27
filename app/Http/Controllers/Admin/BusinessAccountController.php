<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessAccount\RejectRequest;
use App\Models\ActivityType;
use App\Models\BusinessAccount;
use App\Models\City;
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
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('city_id'), function ($query) use ($request) {
                $query->where('city_id', $request->city_id);
            })
            ->when($request->filled('activity_type_id'), function ($query) use ($request) {
                $query->where('activity_type_id', $request->activity_type_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('license_number', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%")
                    ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $cities = City::query()->orderBy('id')->get();
        $activityTypes = ActivityType::query()->orderBy('id')->get();

        return view('admin.business-accounts.index', compact(
            'businessAccounts',
            'cities',
            'activityTypes'
        ));
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
