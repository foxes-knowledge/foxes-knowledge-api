<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
            'title' => 'required|string|max:64',
            'body' => 'required|string|max:65500',
            'user_id' => 'required|integer|exists:users,id',
            'post_id' => 'integer|exists:posts,id',
            'upvotes' => 'integer',
            'downvotes' => 'integer'
        ];
    }
}