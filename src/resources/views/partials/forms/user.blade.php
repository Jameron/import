<div class="form-group">
    <label>Name: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="name" value="@if(isset($user)){{$user->name}}@endif">
</div>
<div class="form-group">
    <label>Email: *</label>
    <input type="text" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif" name="email" value="@if(isset($user)){{$user->email}}@endif">
</div>
<div class="form-group">
    <label for="password">Password</label>
    <input type="password" name="password" id="password" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif">
</div>
<div class="form-group">
    <label for="confirm">Confirm Password</label>
    <input type="password" name="password_confirmation" id="confirm" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif">
</div>
@if(config('enrollments.options.has_companies'))
<div class="form-group">
	<label>Company<span class="req">*</span></label>
    <select name="company_id" class="form-control @if(config('admin.theme')=='dark')form-control-dark @endif">
        @if(isset($companies))
            @foreach($companies as $id => $company)
                <option value="{{$id}}" @if(isset($user) && $user->company_id==$id) selected @endif>{{$company}}</option>
        @endforeach
        @endif
    </select>
</div>
@endif
@if(isset($roles))
<div class="form-group">
    <fieldset class="group">
        <legend>Select role(s)</legend>
        <ul class="unstyled">
            @if(count($roles))
                @foreach($roles as $role)
                    <div class="checkbox">
                    <label for="{{$role->id}}">
                        <input type="checkbox" name="roles[]" value="{{$role->id}}"  id="{{$role->id}}" {{ in_array($role->id, $array = isset($user) ? $user->roles->pluck('id')->toArray() : []) ? 'checked' : ''}}>
                    {{ $role->name }}
                    </label>
                    </div>
                @endforeach
            @endif
        </ul>
    </fieldset>
</div>
@endif
<p class="button-group">
    <button type="submit" class="btn btn-primary">Save</button>
	<a href="{{ url('admin/users') }}" class="btn-alt">Cancel</a>
</p>
