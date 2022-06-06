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
            'username' => 'required|string|max:16',
            'name' => 'string|max:32',
            'email' => 'required|string|max:64|unique:email',
            'password' => 'required|string|max:64',
            'isEmailPublic' => 'boolean',
            'picture' => 'image|mimes:png,jpg,jpeg|max:4096',
            'bio' => 'string|max:200',
            'color' => 'string|max:7',
        ];
    }
}
