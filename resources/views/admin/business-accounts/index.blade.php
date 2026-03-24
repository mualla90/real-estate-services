@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Business Accounts</strong>
        <button class="btn btn-sm btn-primary">Add New</button>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" placeholder="Search by business name">
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option>Status</option>
                    <option>Pending</option>
                    <option>Approved</option>
                    <option>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select">
                    <option>City</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Business Name</th>
                        <th>Owner</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No data yet</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
