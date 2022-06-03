<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
            'title' => 'max:64|string',
            'body' => 'string|max:65500',
            'user_id' => 'exists:users,id',
            'post_id' => 'exists:posts,id',
            'upvotes' => 'integer',
            'downvotes' => 'integer'
        ];
    }
}
