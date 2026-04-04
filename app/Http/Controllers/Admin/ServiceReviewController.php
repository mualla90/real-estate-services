<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ApproveServiceRequest;
use App\Http\Requests\Admin\Service\RejectServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceReviewController extends Controller
{
    public function __construct(
        protected ServiceService $serviceService
    ) {
    }

    public function index(Request $request): View
    {
        abort_unless(auth('admin')->user()->can('services.view'), 403);

        $services = Service::query()
            ->with(['businessAccount', 'category', 'subcategory', 'city'])

            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            }, function ($query) {
                $query->where('status', 'pending');
            })

            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', (bool) $request->is_active);
            })

            ->when($request->filled('business_account_id'), function ($query) use ($request) {
                $query->where('business_account_id', $request->business_account_id);
            })

            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })

            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.review.index', compact('services'));
    }

    public function show(Service $service): View
    {
        abort_unless(auth('admin')->user()->can('services.view'), 403);

        $service->load(['businessAccount', 'category', 'subcategory', 'city']);

        return view('admin.services.review.show', compact('service'));
    }

    public function approve(ApproveServiceRequest $request, Service $service): RedirectResponse
    {
        $this->serviceService->approve($service, auth('admin')->user());

        return redirect()
            ->route('admin.services.review.index')
            ->with('success', 'Service approved successfully.');
    }

    public function reject(RejectServiceRequest $request, Service $service): RedirectResponse
    {
        $this->serviceService->reject(
            $service,
            auth('admin')->user(),
            $request->validated('rejection_reason')
        );

        return redirect()
            ->route('admin.services.review.index')
            ->with('success', 'Service rejected successfully.');
    }
    public function activate(Service $service): RedirectResponse
    {
        abort_unless(auth('admin')->user()->can('services.activate'), 403);

        $this->serviceService->activate($service);

        return redirect()
            ->back()
            ->with('success', 'Service activated successfully.');
    }

    public function deactivate(Service $service): RedirectResponse
    {
        abort_unless(auth('admin')->user()->can('services.deactivate'), 403);

        $this->serviceService->deactivate($service);

        return redirect()
            ->back()
            ->with('success', 'Service deactivated successfully.');
    }
}
