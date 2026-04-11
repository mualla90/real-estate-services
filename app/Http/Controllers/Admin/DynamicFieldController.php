<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DynamicField\StoreRequest;
use App\Http\Requests\Admin\DynamicField\UpdateRequest;
use App\Models\Category;
use App\Models\DynamicField;
use App\Models\Subcategory;
use App\Services\DynamicField\DynamicFieldService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DynamicFieldController extends Controller
{
    public function __construct(
        protected DynamicFieldService $service
    ) {
    }

    public function index(Request $request): View
    {
        $dynamicFields = DynamicField::query()
            ->with(['category', 'subcategory'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('field_key', 'like', "%{$search}%")
                        ->orWhere('name->en', 'like', "%{$search}%")
                        ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.dynamic-fields.index', compact('dynamicFields'));
    }

    public function create(): View
    {
        $categories = Category::query()->latest('id')->get();
        $subcategories = Subcategory::query()->with('category')->latest('id')->get();

        return view('admin.dynamic-fields.create', compact('categories', 'subcategories'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_required'));
        $data['admin_id'] = auth('admin')->id();

        $this->service->create($data);

        return redirect()
            ->route('admin.dynamic-fields.index')
            ->with('success', __('admin.dynamic_field_created_successfully'));
    }

    public function edit(DynamicField $dynamicField): View
    {
        $categories = Category::query()->latest('id')->get();
        $subcategories = Subcategory::query()->with('category')->latest('id')->get();

        return view('admin.dynamic-fields.edit', compact('dynamicField', 'categories', 'subcategories'));
    }

    public function update(UpdateRequest $request, DynamicField $dynamicField): RedirectResponse
    {
        $data = $this->preparePayload($request->validated(), $request->boolean('is_required'));

        $this->service->update($dynamicField, $data);

        return redirect()
            ->route('admin.dynamic-fields.index')
            ->with('success', __('admin.dynamic_field_updated_successfully'));
    }

    public function destroy(DynamicField $dynamicField): RedirectResponse
    {
        $this->service->delete($dynamicField);

        return redirect()
            ->route('admin.dynamic-fields.index')
            ->with('success', __('admin.dynamic_field_deleted_successfully'));
    }

    protected function preparePayload(array $validated, bool $isRequired): array
    {
        $payload = $validated;

        $optionsText = trim((string) ($payload['options_text'] ?? ''));
        unset($payload['options_text']);

        $payload['category_id'] = $payload['category_id'] ?: null;
        $payload['subcategory_id'] = $payload['subcategory_id'] ?: null;
        $payload['is_required'] = $isRequired;
        $payload['sort_order'] = $payload['sort_order'] ?? 0;
        $payload['status'] = $payload['status'] ?? 'active';

        if ($optionsText !== '') {
            $payload['options'] = collect(preg_split('/\r\n|\r|\n/', $optionsText))
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->all();
        } else {
            $payload['options'] = null;
        }

        return $payload;
    }
}
