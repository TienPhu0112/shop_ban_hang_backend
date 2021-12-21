<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends LoginRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                'first_name' => 'required|alpha',
                'last_name' => 'required|alpha',
                'avatar' => 'nullable|mimes:png,jpg,jpeg',
                'phone_num' => 'required',
                'province' => 'required',
                'district' => 'required',
                'address' => 'required',
                'email' => 'required|email|unique:users,email'
            ]
        );
    }
}
