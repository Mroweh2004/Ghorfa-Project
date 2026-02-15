<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'property_id' => 'required|integer|exists:properties,id',
            'type' => 'required|string|in:buy,rent',
            'start_date' => 'required_if:type,rent|date',
            'end_date' => 'required_if:type,rent|date|after:start_date',
            'rules_accepted' => 'boolean',
            'rules_exceptions' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'property_id.required' => 'Property is required.',
            'property_id.exists' => 'The selected property does not exist.',
            'type.required' => 'Transaction type is required.',
            'type.in' => 'Transaction type must be either "buy" or "rent".',
            'start_date.required_if' => 'Start date is required for rental transactions.',
            'end_date.required_if' => 'End date is required for rental transactions.',
            'end_date.after' => 'End date must be after start date.',
        ];
    }
}
