@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Create Activity Type</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.activity-types.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">English Name</label>
                    <input type="text" name="name[en]" class="form-control" value="{{ old('name.en') }}">
                    @error('name.en')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Arabic Name</label>
                    <input type="text" name="name[ar]" class="form-control" value="{{ old('name.ar') }}">
                    @error('name.ar')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" name="status" value="1" class="form-check-input" id="status" checked>
                    <label class="form-check-label" for="status">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.activity-types.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
