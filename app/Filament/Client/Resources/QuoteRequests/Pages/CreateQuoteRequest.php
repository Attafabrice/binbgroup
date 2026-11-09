<?php

namespace App\Filament\Client\Resources\QuoteRequests\Pages;

use App\Enums\DocumentOrigin;
use App\Enums\DocumentType;
use App\Enums\FieldType;
use App\Filament\Client\Resources\QuoteRequests\QuoteRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CreateQuoteRequest extends CreateRecord
{
    protected static string $resource = QuoteRequestResource::class;

    protected ?string $heading = 'Nouvelle demande de devis';

    protected static bool $canCreateAnother = false;

    /** @var array<int, string> */
    protected array $attachments = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // « attachments » n'est pas une colonne : on le garde pour afterCreate()
        $this->attachments = array_filter(Arr::wrap(Arr::pull($data, 'attachments')));

        $client = auth()->user()->client;

        return [
            ...$data,
            'client_id' => $client->id,
            'name' => $client->display_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'ip_address' => request()->ip(),
        ];
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $answers = $record->answers ?? [];

        // Pièces demandées par le service
        foreach ($record->service->fields->where('type', FieldType::File) as $field) {
            foreach (Arr::wrap($answers[$field->name] ?? []) as $path) {
                if ($this->storeDocument($path, $field->label)) {
                    $answers[$field->name] = $this->originalName($path);
                }
            }
        }

        // Autres documents
        foreach ($this->attachments as $path) {
            $this->storeDocument($path, 'Pièce jointe');
        }

        $record->update(['answers' => $answers]);
    }

    private function originalName(string $path): string
    {
        return Str::after(basename($path), '_');
    }

    private function storeDocument(?string $path, string $title): bool
    {
        $disk = Storage::disk('local');

        if (blank($path) || ! $disk->exists($path)) {
            return false;
        }

        $this->record->documents()->create([
            'origin' => DocumentOrigin::Client,
            'type' => DocumentType::PieceJustificative,
            'title' => $title,
            'disk' => 'local',
            'file_path' => $path,
            'original_name' => $this->originalName($path),
            'mime_type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'size' => $disk->size($path),
            'checksum' => hash_file('sha256', $disk->path($path)),
            'is_visible_to_client' => true,
            'uploaded_by' => auth()->id(),
        ]);

        return true;
    }

    protected function getCreatedNotificationTitle(): ?string{
        return 'Votre demande a bien été envoyée. Nous revenons vers vous rapidement.';
    }
}