@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.create_city')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.store') }}">
                @csrf

                @include('admin.cities._form')

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">
                    {{ __('admin.save') }}
                </button>
                    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






