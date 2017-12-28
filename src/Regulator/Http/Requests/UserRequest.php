<?php 

namespace Jameron\Regulator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
                'email'    => 'required|email|unique:users',
                'password' => 'sometimes|nullable|alpha_num|between:6,12',
                'password_confirmation' => 'sometimes|nullable|same:password|alpha_num|between:6,12',
            ];
        }
        case 'PUT':
        case 'PATCH':
        {
         
            $id = $this->route('user');
            return [
                'name'    => 'required',
                'email'    => 'required|email|unique:users,email,' . $id,
                'password' => 'sometimes|nullable|alpha_num|between:6,12',
                'password_confirmation' => 'sometimes|nullable|same:password|alpha_num|between:6,12',
            ];
        }
        default: break;
        }
    }
}
