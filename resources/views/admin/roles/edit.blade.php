@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">{{ __('admin.edit_role') }}</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')

                @include('admin.roles._form')

                <button type="submit" class="btn btn-primary">
                    {{ __('admin.update') }}
                </button>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
