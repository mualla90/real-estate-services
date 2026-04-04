@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-4">{{ __('admin.edit') }}</h3>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.cities.update', $city) }}">
                @csrf
                @method('PUT')

                @include('admin.cities._form')

                <button type="submit" class="btn btn-primary">
                    {{ __('admin.update') }}
                </button>

                <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">
                    {{ __('admin.cancel') }}
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
