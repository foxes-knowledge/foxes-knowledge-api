<?php

namespace App\Http\Requests\TagRequest;

use Illuminate\Foundation\Http\FormRequest;

class TagStoreRequest extends FormRequest
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
            'tag_id' => 'integer|exists:tags,id',
            'name' => 'required|string|max:24',
            'color' => 'required|string|max:7',
        ];
    }
}
