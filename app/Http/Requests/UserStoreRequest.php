<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|max:16|string',
            'name' => 'nullable|max:32|string',
            'email' => 'unique:email|max:64|required|string',
            'password' => 'required|max:64',
            'isEmailPublic' => 'boolean',
            'picture' => 'mimes:png,jpg,jpeg|max:4096',
            'bio' => 'max:200',
            'color' => 'max:7',
        ];
    }
}
