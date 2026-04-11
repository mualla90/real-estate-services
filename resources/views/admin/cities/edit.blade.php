@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.edit')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.update', $city) }}">
                @csrf
                @method('PUT')

                @include('admin.cities._form')

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">
                    {{ __('admin.update') }}
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






