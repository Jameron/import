<div class="form-group">
    <label>Name: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="name" value="@if(isset($role)){{$role->name}}@endif">
</div>
<div class="form-group">
    <label>Slug: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="slug" value="@if(isset($role)){{$role->slug}}@endif">
</div>
<div id="regulator-app">
    <permissions :php_permissions='{{ $permissions->toJson() }}' :php_existing_permissions='{{ isset($role) ? $role->permissions->toJson() : null }}'></permissions>
</div>
<p class="button-group">
    <button type="submit" class="btn btn-primary">Save</button>
	<a href="{{ url(config('regulator.role.resource_route')) }}" class="btn-alt">Cancel</a>
</p
