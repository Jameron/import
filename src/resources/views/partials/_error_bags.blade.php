@if(count($error_bags) > 0) 
    <div class="alert alert-warning" role="alert">
        @foreach($error_bags as $error_bag) 
            @foreach($error_bag->all() as $error) 
                {{ $error }}
            @endforeach
        @endforeach
    </div>
@endif
