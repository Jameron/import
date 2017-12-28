@extends('admin::layouts.app')
@section('content')
    @component('admin::partials._card')
        @slot('header')
            Import CSV
        @endslot
        @slot('body')
            @if(Gate::check('upload_csv'))
                <form class="text-left" action="{{ url(config('import.post_import_route')) }}" method="POST" type="file" accept=".csv">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @include('import::partials.forms.import', ['submitButtonText' => 'Import', 'mode'=>'create'])
                </form>
            @endif
        @endslot
    @endcomponent
@endsection
