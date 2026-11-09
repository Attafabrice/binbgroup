<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\ProjectPhoto;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PrivateFileController extends Controller
{
    public function photo(ProjectPhoto $photo): StreamedResponse{
        $user = auth()->user();
        $allowed = $user->is_active && (
            $user->isStaff()
            || ($user->isClient()
                && $photo->is_visible_to_client
                && $photo->project?->client_id === $user->client?->id)
        );

        abort_unless($allowed && Storage::disk($photo->disk)->exists($photo->path), 404);
        return Storage::disk($photo->disk)->response($photo->path);
    }

    public function document(Document $document): StreamedResponse{
        Gate::authorize('download', $document);
        abort_unless(Storage::disk($document->disk)->exists($document->file_path), 404);
        return $document->download();
    }
}