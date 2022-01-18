<?php

namespace App\Http\Requests\GithubRepositories;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'search' => ['sometimes', 'max:100'],
        ];
    }
}
