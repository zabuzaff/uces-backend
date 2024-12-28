<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class NotificationApiController extends Controller
{
    public function getNotifications()
    {
        try {
            $data = auth()->user()->notifications;
            return response()->json([
                'success' => true,
                'message' => 'Data fetched successfully.',
                'data' => $data,
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching the data.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function markAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
        ], Response::HTTP_OK);
    }
}
