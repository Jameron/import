<?php 

namespace Jameron\Regulator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        switch ($this->method()) {
        case 'GET':
        {
        }
        case 'DELETE':
        {
            return [];
        }
        case 'POST':
        {
            return [
                'name'    => 'required',
                'slug'    => 'required:unique:regulator_permissions',
            ];
        }
        case 'PUT':
        case 'PATCH':
        {
         
            $id = $this->route('role');
            return [
                'name'    => 'required',
                'slug'    => 'required:unique:regulator_permissions,' . $id,
            ];
        }
        default: break;
        }
    }
}
