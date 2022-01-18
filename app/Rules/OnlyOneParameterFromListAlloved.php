<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class OnlyOneParameterFromListAlloved implements Rule
{
    protected array $fieldNames;

    protected FormRequest $formRequest;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(FormRequest $request, array $fieldNames)
    {
        $this->fieldNames = $fieldNames;
        $this->formRequest = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return count($this->formRequest->only($this->fieldNames)) <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Please use only one attribute from the list provided at the same time: :list', ['list' => implode(', ', $this->fieldNames)]);
    }
}
