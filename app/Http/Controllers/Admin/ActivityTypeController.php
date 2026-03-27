<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ActivityType\StoreRequest;
use App\Http\Requests\Admin\ActivityType\UpdateRequest;
use App\Models\ActivityType;
use App\Services\ActivityType\ActivityTypeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityTypeController extends Controller
{
    public function __construct(
        protected ActivityTypeService $service
    ) {}

    public function index(Request $request): View
    {
        $activityTypes = ActivityType::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name->en', 'like', "%{$search}%")
                      ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.activity-types.index', compact('activityTypes'));
    }

    public function create(): View
    {
        return view('admin.activity-types.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->create($data);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type created successfully.');
    }

    public function edit(ActivityType $activityType): View
    {
        return view('admin.activity-types.edit', compact('activityType'));
    }

    public function update(UpdateRequest $request, ActivityType $activityType): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($activityType, $data);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type updated successfully.');
    }

    public function destroy(ActivityType $activityType): RedirectResponse
    {
        $this->service->delete($activityType);

        return redirect()
            ->route('admin.activity-types.index')
            ->with('success', 'Activity type deleted successfully.');
    }
}
