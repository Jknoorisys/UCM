<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ManageNotifications extends Controller
{
    // By Javeriya Kauser
    public function getNotifications(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_number'   => 'required||numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.validation'),
                    'errors'    =>  $validator->errors(),
                ], 400
            );
        } 

        try {
            $per_page = 10;
            $page_number = $request->input(key:'page_number', default:1);

            $admin  = Admin::first();
            if (empty($admin)) {
                 return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.details.not-found'),
                ], 400);
            }

            $notifications = $admin->unreadNotifications()
                                    ->offset(($page_number - 1) * $per_page)
                                    ->limit($per_page)
                                    ->get();

            $total =  $admin->unreadNotifications()->count();                      
            if (!$notifications->isEmpty()) {
                $notification = [];
                foreach ($notifications as $notify) {
                    $notification[] = $notify->data;
                }
                return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.list.success'),
                        'total'     => $total,
                        'data'      => $notification,
                ], 200);
            } else {
                return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.list.failed'),
                        'data'      => $notifications
                ], 200);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function sendNotification(Request $request) {
        $validator = Validator::make($request->all(), [
            'title'   => 'required',
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.validation'),
                    'errors'    =>  $validator->errors(),
                ], 400
            );
        } 

        try {
            $message = [
                'title' => $request->title,
                'msg'   => $request->message
            ];      
           
           $users = User::where([['status', '=', 'active'], ['is_verified', '=', 'yes']])->get();
            if (!$users->isEmpty()) {
                foreach ($users as $user) {
                   $user->notify(new UserNotification($message));
                }
                return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.notification.success'),
                ], 200);
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.notification.failed'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
