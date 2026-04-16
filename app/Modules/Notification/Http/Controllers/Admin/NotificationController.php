<?php

namespace App\Modules\Notification\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest('created_at')->take(10)->get();
        return view('admin.pages.notifications', compact('notifications'));
    }
}
