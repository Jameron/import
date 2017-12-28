<form action="{{ $search['route'] }}" method="GET" class="mb-4">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <label class="sr-only" for="inlineFormInputName2">Search</label>
    <div class="input-group">
        <input type="text" name="search_string" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif col-md-12" placeholder="{{ $search['placeholder'] }}" aria-label="Search for..." value="@if($search_string){{ ($search_string) }}@endif">
        <span class="input-group-btn">
            <button class="btn btn-secondary" type="submit">@if(isset($search['icon']))<i class="fa fa-{{ $search['icon']}}" aria-hidden="true"></i>@endif {{$search['button_text']}}</button>
        </span>
    </div>
</form>
