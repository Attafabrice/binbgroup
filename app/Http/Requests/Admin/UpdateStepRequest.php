<?php

namespace App\Http\Requests\Admin;

use App\Enums\StepStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStepRequest extends FormRequest
{
     // l'autorisation est faite dans le contrôleur
    public function authorize(): bool{
        return true;
    }

    public function rules(): array{
        return [
            'status'       => ['required', Rule::enum(StepStatus::class)],
            'planned_date' => ['nullable', 'date'],
        ];
    }
}