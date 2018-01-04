<?php 

namespace Jameron\Import\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest {

	public function messages()
	{
		return [
			'csv.required'	 => 'Missing a csv file.',
			'csv.mimes'		 => 'Incorrect file format, please upload a csv.',
		];
	}

	public function authorize()
	{
		return true;
	}

	public function rules()
	{
		switch($this->method())
   	 	{
			case 'GET':
        	case 'DELETE':
        	{
            	return [];
        	}
			case 'POST':
			{
				return 	
				[
			       'csv' => 'required|mimes:csv,txt',
    			];
			}
			case 'PUT':
			case 'PATCH':
			{
				return 	
				[
			       'csv' => 'required|mimes:csv,txt',
    			];
			}
			default: break;
		}
	}
}
