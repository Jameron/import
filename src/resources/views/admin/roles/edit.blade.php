@extends('admin::layouts.app')
@section('content')
    @component('admin::partials._card')
        @slot('header')
            Edit Role
        @endslot
        @slot('body')
            @if(Gate::check('update_roles') )
                <form action="{{ url(config('regulator.role.resource_route') . '/' . $role->id) }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    @include('regulator::partials.forms.role', ['submitButtonText' => 'Update', 'mode'=>'edit'])
                </form>
                @if(Gate::check('delete_roles'))
                    @include('partials._delete_button', [
                        'route'=> config('regulator.role.resource_route') . '/' . ($role->id),
                        'button_text'=>''
                    ])
                @endif
            @endif
        @endslot
    @endcomponent
@endsection
@section('js')
    @if(Auth::check() && Auth::user()->roles()->first()->slug==='admin')
    <script src="/js/Regulator.js"></script>
    @endif
@endsection
