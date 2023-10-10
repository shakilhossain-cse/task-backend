<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;

class NotificationController extends Controller
{

    public function index() {
        $user = Auth::user();
        $notifications = $user->notifications->where('read', 0);
        return response()->json($notifications);
    }
}
