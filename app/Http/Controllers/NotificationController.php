<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

use Auth;

class NotificationController extends Controller
{
    public function index()
    {
        
    }
    
    public function updateStatus(Request $request)
    {
        try {
            $user = Auth::user();
            if(!$user){
                return response()->json(['message' => 'Cant Access'], 412);
            }
            $notification = Notification::find($request->notifId);
            $notification->has_read = 1;
            $notification->save();
            return response()->json(['message' => 'success'], 200);

        } catch (\Throwable $e) {
            Log::error($e);
            $message = $e->getMessage();
            return response()->json(['message' => $message], 500);
        }  
    }
    
}
