@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">Create City</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.store') }}">
                @csrf

                @include('admin.cities._form')

                <button type="submit" class="btn btn-primary">
                    Save
                </button>

                <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
