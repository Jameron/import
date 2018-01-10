@if(isset($new_models_count))
    <h3>{{ $new_models_count }} new {{ str_plural( $model_name, $new_models_count) }}</h3>
@endif
@if(isset($new_related_models))
    @foreach($new_related_models as $model => $count)
    <h3> {{ $count }} new {{ str_plural($model, $count) }}</h3>
    @endforeach
@endif
@if(isset($num_skipped_rows))
    <h3>{{ $num_skipped_rows }} rows skipped</h3>
@endif
@if(isset($error_bags) && count($error_bags) > 0) 
    <table class="table">
    <thead>
      <tr>
        <th>Row</th>
        <th>Error</th>
      </tr>
    </thead>
        @foreach($error_bags as $error_bag) 
            <tbody>
                @if($error_bag)
            @foreach($error_bag->toArray() as $field => $error) 
                @if($field!=='row_error')
                    <tr>
                        <td>{!! $error_bag->first('row_error') !!}</td>
                        <td>{{ $error[0] }}</td>
                    </tr>
                @endif
            @endforeach
        @endif
            </tbody>
        @endforeach
    </table>
@endif

{{--
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
@endif--}}
