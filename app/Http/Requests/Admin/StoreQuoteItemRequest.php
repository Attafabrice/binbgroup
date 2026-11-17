<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteItemRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array{
        return [
            'designation' => ['required', 'string', 'max:255'],
            'quantity'    => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'unit'        => ['nullable', 'string', 'max:20'],
            'unit_price'  => ['required', 'integer', 'min:0', 'max:1000000000'],
        ];
    }
}
