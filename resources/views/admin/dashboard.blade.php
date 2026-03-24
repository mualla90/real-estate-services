@extends('layouts.admin')

@section('content')
<div class="card mb-4">
    <div class="card-body">
        <h1 class="h3 mb-3">Dashboard</h1>
        <p class="mb-0">CoreUI is installed successfully.</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="text-medium-emphasis small text-uppercase fw-semibold">Users</div>
                <div class="fs-4 fw-semibold">0</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="text-medium-emphasis small text-uppercase fw-semibold">Pending Business Accounts</div>
                <div class="fs-4 fw-semibold">0</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-body">
                <div class="text-medium-emphasis small text-uppercase fw-semibold">Pending Services</div>
                <div class="fs-4 fw-semibold">0</div>
            </div>
        </div>
    </div>
</div>
@endsection
