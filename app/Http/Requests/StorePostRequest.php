<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {

        return auth()->check();

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
                    'title' => 'required|string|max:255',
                    'body'  => 'required|string',
                    'status' => 'in:draft,published',
                ];

    }

    public function messages(): array
    {
        return [
            'title.required' => 'A title is required.',
            'body.required'  => 'The body cannot be empty.',
            'status.in'       => 'Status must be either draft or published.',
        ];
    }

}
