@extends('admin::layouts.app')
@section('content')
    @component('admin::partials._card')
        @slot('header')
            Create Permission
        @endslot
        @slot('body')
            @if(Gate::check('create_permissions'))
                <form class="text-left" action="{{ url(config('regulator.permission.resource_route')) }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @include('regulator::partials.forms.permission', ['submitButtonText' => 'Update', 'mode'=>'create'])
                </form>
            @endif
        @endslot
    @endcomponent
@endsection
