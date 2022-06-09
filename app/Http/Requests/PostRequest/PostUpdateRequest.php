<?php

namespace App\Http\Requests\PostRequest;

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
            'title' => 'string|max:64',
            'content' => 'string|max:65500',
            'user_id' => 'integer|exists:users,id',
            'post_id' => 'integer|exists:posts,id',
            'tag_ids' => 'array',
            'attachments.*' => 'max:8096'
        ];
    }
}
