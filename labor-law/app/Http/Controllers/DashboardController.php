<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $history = collect();
        if (Auth::check()) {
            $history = Chat::query()
                ->where('user_id', Auth::id())
                ->latest('id')
                ->take(10)
                ->get(['id', 'message']);
        }

        return view('dashboard', [
            'history' => $history,
        ]);
    }
}
