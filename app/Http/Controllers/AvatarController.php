<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function __invoke(User $user){
        $viewer = auth()->user();

        // soi-même, ou le staff ; un client ne voit pas les photos des autres comptes
        abort_unless($viewer->id === $user->id || $viewer->isStaff(), 404);
        abort_unless($user->avatar_path && Storage::disk('local')->exists($user->avatar_path), 404);

        return Storage::disk('local')->response($user->avatar_path);
    }
}