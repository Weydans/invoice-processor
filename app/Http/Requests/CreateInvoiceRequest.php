<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'min:3'],
            'items.*.value' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'items.required' => 'An invoice must have at least one item.',
            'items.array' => 'The items field must be an array.',
            'items.min' => 'At least one invoice item is required.',
            'items.*.description.required' => 'Each item must have a description.',
            'items.*.value.required' => 'Each item must have a value.',
            'items.*.value.numeric' => 'The item value must be a number.',
            'items.*.value.min' => 'The item value must be greater than zero.',
        ];
    }
}
