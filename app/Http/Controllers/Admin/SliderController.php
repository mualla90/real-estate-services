<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Slider\StoreRequest;
use App\Http\Requests\Admin\Slider\UpdateRequest;
use App\Models\Slider;
use App\Services\Slider\SliderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SliderController extends Controller
{
    public function __construct(
        protected SliderService $service
    ) {
    }

    public function index(Request $request): View
    {
        $sliders = Slider::query()
            ->with('media')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('title->en', 'like', "%{$search}%")
                        ->orWhere('title->ar', 'like', "%{$search}%")
                        ->orWhere('subtitle->en', 'like', "%{$search}%")
                        ->orWhere('subtitle->ar', 'like', "%{$search}%")
                        ->orWhere('link', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $status = $request->input('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                }
                if ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('admin.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        return view('admin.sliders.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_active'));
        $data['created_by_admin_id'] = auth('admin')->id();

        $this->service->create($data, $request->file('image'));

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_created_successfully'));
    }

    public function edit(Slider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(UpdateRequest $request, Slider $slider): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_active'));

        $this->service->update($slider, $data, $request->file('image'));

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_updated_successfully'));
    }

    public function destroy(Slider $slider): RedirectResponse
    {
        $this->service->delete($slider);

        return redirect()
            ->route('admin.sliders.index')
            ->with('success', __('admin.slider_deleted_successfully'));
    }

    protected function preparePayload(array $validated, bool $isActive): array
    {
        $payload = $validated;

        unset($payload['image']);
        $payload['is_active'] = $isActive;
        $payload['sort_order'] = $payload['sort_order'] ?? 0;

        if (empty($payload['subtitle']['en'] ?? null) && empty($payload['subtitle']['ar'] ?? null)) {
            $payload['subtitle'] = null;
        }

        return $payload;
    }
}
