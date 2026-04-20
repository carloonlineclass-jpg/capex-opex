<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(12);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(string $notification): RedirectResponse
    {
        $record = Auth::user()->notifications()->whereKey($notification)->firstOrFail();
        if (!$record->read_at) {
            $record->markAsRead();
        }
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
