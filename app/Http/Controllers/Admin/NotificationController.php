<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function index(){
        return view('admin.notifications.index', [
            'items' => auth()->user()->notifications()->paginate(20),
        ]);
    }
    public function summary(){
        $u = auth()->user();

        return response()->json([
            'notifications' => $u->unreadNotifications()->count(),
            'messages'      => $u->can('view_contact_message')
                ? \App\Models\ContactMessage::whereNull('read_at')->count() : 0,
            'latest'        => $u->unreadNotifications()->latest()->first()?->data['title'],
        ]);
    }
    
    public function open(string $id){
        $n = auth()->user()->notifications()->findOrFail($id); // uniquement les siennes
        $n->markAsRead();
        return redirect($n->data['url'] ?? route('admin.dashboard'));
    }

    public function readAll(){
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}