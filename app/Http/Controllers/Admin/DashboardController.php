<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Report;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $recentActivities = $this->buildRecentActivities();

        return view('admin.dashboard', [
            'stats' => $this->buildStats(),
            'recentActivities' => $recentActivities,
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'stats' => $this->buildStats(),
        ]);
    }

    private function buildRecentActivities(): Collection
    {
        $reportItems = Report::query()->latest('created_at')->take(4)->get()->map(function (Report $report) {
            return [
                'type' => 'report',
                'title' => __('admin.activity_new_report', ['id' => $report->id]),
                'time' => $report->created_at,
                'url' => route('admin.reports.show', $report),
            ];
        })->toBase();

        $businessAccountItems = BusinessAccount::query()->latest('created_at')->take(4)->get()->map(function (BusinessAccount $account) {
            return [
                'type' => 'business_account',
                'title' => __('admin.activity_new_business_account', ['id' => $account->id]),
                'time' => $account->created_at,
                'url' => route('admin.business-accounts.show', $account),
            ];
        })->toBase();

        $serviceItems = Service::query()->latest('created_at')->take(4)->get()->map(function (Service $service) {
            return [
                'type' => 'service',
                'title' => __('admin.activity_new_service', ['id' => $service->id]),
                'time' => $service->created_at,
                'url' => route('admin.services.review.show', $service),
            ];
        })->toBase();

        return collect()
            ->concat($reportItems)
            ->concat($businessAccountItems)
            ->concat($serviceItems)
            ->sortByDesc('time')
            ->take(8)
            ->values();
    }

    private function buildStats(): array
    {
        $admin = auth('admin')->user();

        return [
            'admins' => Admin::query()->count(),
            'pending_business_accounts' => BusinessAccount::query()->where('status', 'pending')->count(),
            'pending_services' => Service::query()->where('status', 'pending')->count(),
            'pending_reports' => Report::query()->where('status', 'pending')->count(),
            'unread_notifications' => $admin
                ? $admin->appNotifications()->whereNull('read_at')->count()
                : 0,
        ];
    }
}
