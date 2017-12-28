@extends('admin::layouts.app')
@section('content')

    @component('admin::partials._card')
        @slot('header')
            Edit Permission
        @endslot
        @slot('body')
            @if(Gate::check('update_permissions') )
                    <form action="{{ url(config('regulator.permission.resource_route') . '/' . $permission->id)}}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PATCH">
                    @include('regulator::partials.forms.permission', ['submitButtonText' => 'Update', 'mode'=>'edit'])
                </form>
                @if(Gate::check('delete_permissions'))
                    @include('partials._delete_button', [
                        'route'=> config('regulator.permission.resource_route') . '/' . ($permission->id),
                        'button_text'=>''
                    ])
                @endif
            @endif
        @endslot
    @endcomponent

@endsection
