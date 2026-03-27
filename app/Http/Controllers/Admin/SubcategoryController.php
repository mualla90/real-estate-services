<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Subcategory\StoreRequest;
use App\Http\Requests\Admin\Subcategory\UpdateRequest;
use App\Models\Category;
use App\Models\Subcategory;
use App\Services\Subcategory\SubcategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    public function __construct(
        protected SubcategoryService $service
    ) {}

    public function index(Request $request): View
    {
        $subcategories = Subcategory::query()
            ->with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name->en', 'like', "%{$search}%")
                      ->orWhere('name->ar', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('id')
            ->get();

        return view('admin.subcategories.index', compact('subcategories', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->create($data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory created successfully.');
    }

    public function edit(Subcategory $subcategory): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(UpdateRequest $request, Subcategory $subcategory): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($subcategory, $data);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory updated successfully.');
    }

    public function destroy(Subcategory $subcategory): RedirectResponse
    {
        $this->service->delete($subcategory);

        return redirect()
            ->route('admin.subcategories.index')
            ->with('success', 'Subcategory deleted successfully.');
    }
}
