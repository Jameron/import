<div class="form-group">
    <label>Name: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="name" value="@if(isset($permission)){{$permission->name}}@endif">
</div>
<div class="form-group">
    <label>Slug: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="slug" value="@if(isset($permission)){{$permission->slug}}@endif">
</div>
<p class="button-group">
    <button type="submit" class="btn btn-primary">Save</button>
	<a href="{{ url(config('regulator.permission.resource_route')) }}" class="btn-alt">Cancel</a>
</p>
