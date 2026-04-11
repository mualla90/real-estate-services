@extends('layouts.admin')

@section('content')
<div class="container-fluid admin-page">
    <x-admin.page-header :title="__('admin.create_slider')" icon-name="sliders">
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">{{ __('admin.back') }}</a>
    </x-admin.page-header>

    <div class="card admin-form-card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.sliders._form')

                <div class="form-actions-sticky">
                    <button type="submit" class="btn btn-primary">{{ __('admin.save') }}</button>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary">{{ __('admin.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
