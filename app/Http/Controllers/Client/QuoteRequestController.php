<?php

namespace App\Http\Controllers\Client;

use App\Enums\{DocumentOrigin, DocumentType, FieldType};
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreQuoteRequestRequest;
use App\Models\{Client, QuoteRequest, Service};
use Illuminate\Http\{Request, UploadedFile};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuoteRequestController extends Controller
{
    private function client(): Client{
        return auth()->user()->client ?? abort(403, 'Aucune fiche client rattachée à ce compte.');
    }

    public function index(){
        $requests = QuoteRequest::with('service')
            ->where('client_id', $this->client()->id)
            ->latest()->paginate(10);
        return view('client.requests.index', compact('requests'));
    }

    public function create(Request $request){
        $this->client();

        return view('client.requests.create', [
            'service'  => Service::active()->with('fields')->find($request->query('service')),
            'services' => Service::active()->get(),
        ]);
    }

    public function store(StoreQuoteRequestRequest $request){
        $client  = $this->client();
        $service = Service::active()->with('fields')->findOrFail($request->input('service_id'));

        $answers = [];
        $uploads = []; // [titre, fichier]

        foreach ($service->fields as $f) {
            if ($f->type === FieldType::File) {
                if ($file = $request->file('answers.'.$f->name)) {
                    $uploads[] = [$f->label, $file];
                    $answers[$f->name] = $file->getClientOriginalName();
                }
                continue;
            }
            $value = $request->input('answers.'.$f->name);
            if (filled($value)) {
                $answers[$f->name] = $value;
            }
        }
        foreach ($request->file('attachments', []) as $file) {
            $uploads[] = ['Pièce jointe', $file];
        }

        $qr = DB::transaction(function () use ($request, $client, $service, $answers, $uploads) {
            $qr = QuoteRequest::create([
                'client_id'  => $client->id,
                'service_id' => $service->id,
                'name'       => $client->display_name,
                'email'      => $client->email ?: $request->user()->email,
                'phone'      => $client->phone,
                'message'    => $request->input('message'),
                'answers'    => $answers,
                'ip_address' => $request->ip(),
            ]);

            foreach ($uploads as [$title, $file]) {
                $this->storeDocument($qr, $file, $title);
            }

            return $qr;
        });
        return redirect()->route('client.requests.show', $qr)
            ->with('success', "Votre demande {$qr->reference} a bien été envoyée. Nous revenons vers vous rapidement.");
    }

    public function show(QuoteRequest $quoteRequest){
        abort_unless($quoteRequest->client_id === $this->client()->id, 404);

        $quoteRequest->load('service.fields', 'visibleDocuments');
        $labels  = $quoteRequest->service->fields->pluck('label', 'name');
        $answers = collect($quoteRequest->answers ?? [])->map(fn ($v, $k) => [
            'label' => $labels[$k] ?? $k,
            'value' => is_array($v) ? implode(', ', $v) : $v,
        ]);
        return view('client.requests.show', ['r' => $quoteRequest, 'answers' => $answers]);
    }

    private function storeDocument(QuoteRequest $qr, UploadedFile $file, string $title): void{
        $original = $file->getClientOriginalName();
        $mime     = $file->getMimeType() ?: 'application/octet-stream'; // détecté côté serveur
        $size     = $file->getSize();
        $hash     = hash_file('sha256', $file->getRealPath());

        // nom généré : jamais le nom d'origine pour le chemin
        $path = $file->storeAs(
            'quote-requests/'.now()->format('Y/m'),
            Str::uuid().'.'.$file->extension(),
            'local'
        );

        $qr->documents()->create([
            'client_id'            => $qr->client_id,
            'origin'               => DocumentOrigin::Client,
            'type'                 => DocumentType::PieceJustificative,
            'title'                => $title,
            'disk'                 => 'local',
            'file_path'            => $path,
            'original_name'        => $original,
            'mime_type'            => $mime,
            'size'                 => $size,
            'checksum'             => $hash,
            'is_visible_to_client' => true,
            'uploaded_by'          => auth()->id(),
        ]);
    }
}