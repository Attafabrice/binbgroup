<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request){
        $this->authorize('viewAny', ContactMessage::class);
        $messages = ContactMessage::query()
            ->when($request->query('filter') === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($request->query('filter') === 'todo', fn ($q) => $q->whereNull('handled_at'))
            ->latest()->paginate(15)->withQueryString();
        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show(ContactMessage $message){
        $this->authorize('view', $message);

        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
        }
        return view('admin.contact-messages.show', ['m' => $message->load('handler')]);
    }

    public function handle(ContactMessage $message){
        $this->authorize('update', $message);
        $message->update(['handled_at' => now(), 'handled_by' => auth()->id()]);
        return back()->with('success', 'Message marqué comme traité.');
    }

    public function destroy(ContactMessage $message){
        $this->authorize('delete', $message);
        $message->delete();
        return redirect()->route('admin.contact-messages.index')->with('success', 'Message supprimé.');
    }
}