<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AlertRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'coin' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!\App\Models\Spot::where('coin', $value)->exists()) {
                        $fail("The selected $attribute is invalid.");
                    }
                },
            ],
            'price' => 'required|numeric',
            'condition' => 'required|in:lower,higher',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'coin.required' => 'The coin field is required.',
            'coin.string' => 'The coin field must be a string.',
            'coin.max' => 'The coin field may not be greater than :max characters.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price field must be a number.',
            'condition.required' => 'The condition field is required.',
            'condition.in' => 'The condition field must be either "lower" or "higher".',
        ];
    }
}
