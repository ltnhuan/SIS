<?php

namespace App\Modules\Notification\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $recipientId = $request->input('recipient_id');

        $query = Notification::query();
        if ($recipientId) {
            $query->where('recipient_id', $recipientId);
        }

        return response()->json([
            'message' => 'Danh sách thông báo.',
            'data' => $query->latest('created_at')->get(),
        ]);
    }
}
