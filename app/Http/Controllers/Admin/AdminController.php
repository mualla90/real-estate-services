<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\StoreRequest;
use App\Http\Requests\Admin\Admin\UpdateRequest;
use App\Models\Admin;
use App\Services\Admin\AdminService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $service
    ) {}

    public function index(Request $request): View
    {
        $admins = Admin::query()
            ->with('roles')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.admins.index', compact('admins'));
    }

    public function create(): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->where('name', '!=', 'super_admin')
            ->orderBy('name')
            ->get();

        return view('admin.admins.create', compact('roles'));
    }

    public function store(StoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->create($data);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin): View
    {
        $roles = Role::query()
            ->where('guard_name', 'admin')
            ->where(function ($query) use ($admin) {
                $query->where('name', '!=', 'super_admin');

                // allow current role if admin already has super_admin
                if ($admin->hasRole('super_admin')) {
                    $query->orWhere('name', 'super_admin');
                }
            })
            ->orderBy('name')
            ->get();

        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    public function update(UpdateRequest $request, Admin $admin): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $this->service->update($admin, $data);

        return redirect()
            ->route('admin.admins.index')
            ->with('success', 'Admin updated successfully.');
    }
}
