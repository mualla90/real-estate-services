@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.create_dynamic_field')" />

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.dynamic-fields.store') }}">
                @csrf

                @include('admin.dynamic-fields._form')

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    <a href="{{ route('admin.dynamic-fields.index') }}" class="btn btn-secondary">{{ __('admin.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






