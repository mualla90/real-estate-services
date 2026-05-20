<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\UpdateStatusRequest;
use App\Models\Report;
use App\Services\Report\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(
        protected ReportService $service
    ) {
    }

    public function index(Request $request): View
    {
        $reports = $this->service->listForAdmin($request->only(['status', 'search', 'per_page']));

        return view('admin.reports.index', compact('reports'));
    }

    public function show(Report $report): View
    {
        $report->load(['reporterBusinessAccount', 'reviewedByAdmin', 'reportable']);

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(UpdateStatusRequest $request, Report $report): RedirectResponse
    {
        $this->service->changeStatus($report, auth('admin')->user(), $request->validated('status'));

        return redirect()
            ->back()
            ->with('success', __('admin.report_status_updated_successfully'));
    }
}
