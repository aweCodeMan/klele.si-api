<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(20);

        return NotificationResource::collection($notifications);
    }

    public function read($uuid, Request $request)
    {
        $notification = $request->user()->markNotificationAsRead($uuid);

        return new NotificationResource($notification->refresh());
    }

    public function allRead(Request $request)
    {
        $notification = $request->user()->markAllNotificationsAsRead();

        return response()->json(['data' => []]);
    }
}
