<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteItemRequest extends FormRequest
{
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'designation' => ['required', 'string', 'max:255'],
            'quantity'    => ['required', 'numeric', 'min:0.01', 'max:100000'],
            'unit'        => ['nullable', 'string', 'max:20'],
            'unit_price'  => ['required', 'integer', 'min:0', 'max:1000000000'],
        ];
    }
}