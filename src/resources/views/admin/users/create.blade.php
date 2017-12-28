@extends('admin::layouts.app')
@section('content')
    @component('admin::partials._card')
        @slot('header')
            Create User
        @endslot
        @slot('body')
            @if(Gate::check('create_roles'))
                <form class="text-left" action="{{ url(config('regulator.user.resource_route')) }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @include('regulator::partials.forms.user', ['submitButtonText' => 'Update', 'mode'=>'create'])
                </form>
            @endif
        @endslot
    @endcomponent
@endsection
