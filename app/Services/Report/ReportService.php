<?php

namespace App\Services\Report;

use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function createForService(BusinessAccount $reporterBusinessAccount, Service $service, array $data): Report
    {
        abort_unless($service->isVisible(), 422, __('api.errors.service_unavailable_for_reporting'));

        return DB::transaction(function () use ($reporterBusinessAccount, $service, $data) {
            return Report::query()->create([
                'reporter_business_account_id' => $reporterBusinessAccount->id,
                'reportable_type' => Service::class,
                'reportable_id' => $service->id,
                'reason' => $data['reason'],
                'description' => $data['description'] ?? null,
                'status' => 'pending',
            ]);
        });
    }

    public function listForAdmin(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 20);

        return Report::query()
            ->with(['reporterBusinessAccount', 'reviewedByAdmin', 'reportable'])
            ->when(! empty($filters['status']), fn ($q) => $q->where('status', $filters['status']))
            ->when(! empty($filters['search']), function ($q) use ($filters) {
                $search = $filters['search'];
                $q->where(function ($sub) use ($search) {
                    $sub->where('reason', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function changeStatus(Report $report, Admin $admin, string $status): Report
    {
        abort_unless(in_array($status, ['reviewed', 'resolved', 'rejected'], true), 422, __('api.errors.invalid_report_status'));

        $report->update([
            'status' => $status,
            'reviewed_by_admin_id' => $admin->id,
            'reviewed_at' => now(),
        ]);

        return $report->fresh(['reporterBusinessAccount', 'reviewedByAdmin', 'reportable']);
    }
}
