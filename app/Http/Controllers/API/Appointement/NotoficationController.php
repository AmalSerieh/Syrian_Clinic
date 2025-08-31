<?php

namespace App\Http\Controllers\API\Appointement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Notification\NotificationResource;
use Illuminate\Http\Request;

class NotoficationController extends Controller
{


    public function all(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير مصادق'
            ], 401);
        }

        $notifications = $user->notifications()->latest()->paginate(20);
        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد إشعارات لعرضها',
                'data' => []
            ], 200);
        }
        return NotificationResource::collection($notifications);
    }

    public function unread(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'message' => 'المستخدم غير مصادق'
            ], 401);
        }

        $notifications = $user->unreadNotifications()->latest()->get();
        if ($notifications->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد إشعارات غير مقروءة',
                'data' => []
            ], 200);
        }
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
        $unreadCount = $request->user()->unreadNotifications->count();

        if ($unreadCount === 0) {
            return response()->json([
                'message' => 'لا توجد إشعارات غير مقروءة لتعليمها',
                'data' => []
            ], 200);
        }
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'تم تعليم كل الإشعارات كمقروءة']);
    }

}
