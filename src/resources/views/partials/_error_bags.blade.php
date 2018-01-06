@if(isset($error_bags) && count($error_bags) > 0) 
    <div class="alert alert-warning" role="alert">
        @foreach($error_bags as $error_bag) 
            @if($error_bag->first('row_error'))
                <p>{!! $error_bag->first('row_error') !!}</p>
            @endif
            @foreach($error_bag->toArray() as $field => $error) 
                @if($field!=='row_error')
                    <p>{{ $error[0] }}</p>
                @endif
            @endforeach
        @endforeach
    </div>
@endif
