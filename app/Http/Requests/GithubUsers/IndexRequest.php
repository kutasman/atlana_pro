<?php

namespace App\Http\Requests\GithubUsers;

use App\Rules\OnlyOneParameterFromListAlloved;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read $search
 * @property-read $endCursor
 * @property-read $startCursor
 */
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
            'search' => ['sometimes', 'string', 'max:50'],
            'startCursor' => [
                'sometimes',
                'string',
                'max:100',
                new OnlyOneParameterFromListAlloved($this, ['endCursor', 'startCursor']),
                ],
            'endCursor' => [
                'sometimes',
                'string',
                'max:100',
                new OnlyOneParameterFromListAlloved($this, ['endCursor', 'startCursor']),
            ]
        ];
    }
}
