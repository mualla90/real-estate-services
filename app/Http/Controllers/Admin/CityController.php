<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\City\StoreRequest;
use App\Http\Requests\Admin\City\UpdateRequest;
use App\Models\City;
use App\Services\City\CityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CityController extends Controller
{
    public function __construct(
        protected CityService $service
    ) {}

    public function index(Request $request): View
    {
        $cities = City::query()
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

        return view('admin.cities.index', compact('cities'));
    }

    public function create(): View
    {
        return view('admin.cities.create');
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(UpdateRequest $request, City $city): RedirectResponse
    {
        $this->service->update($city, $request->validated());

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city): RedirectResponse
    {
        $this->service->delete($city);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City deleted successfully.');
    }
}
