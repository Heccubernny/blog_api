<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        // Only allow owner of post
        $post = $this->route('post');
        return $post && $post->user_id === auth()->id();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
                    'title' => 'sometimes|string|max:255',
                    'body'  => 'sometimes|string',
                    'status' => 'in:draft,published',
                ];

    }

}
