<?php

namespace App\Http\Requests\Client;

use App\Enums\FieldType;
use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuoteRequestRequest extends FormRequest
{
    private const FILE = ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'];

    public function authorize(): bool { return true; }

    private function service(): ?Service{
        return Service::active()->with('fields')->find($this->input('service_id'));
    }

    public function rules(): array{
        $rules = [
            'service_id'    => ['required', Rule::exists('services', 'id')->where('is_active', true)],
            'message'       => ['nullable', 'string', 'max:3000'],
            'attachments'   => ['nullable', 'array', 'max:5'],
            'attachments.*' => self::FILE,
        ];

        foreach ($this->service()?->fields ?? [] as $f) {
            $rules['answers.'.$f->name] = [
                $f->is_required ? 'required' : 'nullable',
                ...match ($f->type) {
                    FieldType::Text     => ['string', 'max:255'],
                    FieldType::Textarea => ['string', 'max:3000'],
                    FieldType::Number   => ['numeric'],
                    FieldType::Date     => ['date'],
                    FieldType::Select   => [Rule::in($f->options ?? [])],
                    FieldType::File     => self::FILE,
                },
            ];
        }

        return $rules;
    }

    public function attributes(): array{
        $attrs = ['attachments' => 'autres documents', 'attachments.*' => 'document', 'message' => 'précisions'];
        foreach ($this->service()?->fields ?? [] as $f) {
            $attrs['answers.'.$f->name] = mb_strtolower($f->label);
        }
        return $attrs;
    }
}