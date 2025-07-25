<?php

namespace App\Http\Controllers\API\Appointement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Notification\NotificationResource;
use Illuminate\Http\Request;

class NotoficationController extends Controller
{


 public function all(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);

        return NotificationResource::collection($notifications);
    }

    public function unread(Request $request)
    {
        $notifications = $request->user()->unreadNotifications()->latest()->get();
        return NotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'تم تعليم الإشعار كمقروء']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم تعليم كل الإشعارات كمقروءة']);
    }

}
