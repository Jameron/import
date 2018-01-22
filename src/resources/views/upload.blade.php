@extends('admin::layouts.app')
@section('content')
    @component('admin::partials._card')
        @slot('header')
            Import CSV
        @endslot
        @slot('body')
            @if(Gate::check('upload_csv'))
                @include('admin::partials._error_bags')
                <form class="text-left" action="{{ $upload_route }}" method="POST" type="file"  enctype="multipart/form-data" accept="{{ $accept }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    @include('import::partials.forms.import', ['submitButtonText' => 'Import', 'mode'=>'create'])
                </form>
            @endif
        @endslot
    @endcomponent
@endsection
