@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.edit_role')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')

                @include('admin.roles._form')

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">
                    {{ __('admin.update') }}
                </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






