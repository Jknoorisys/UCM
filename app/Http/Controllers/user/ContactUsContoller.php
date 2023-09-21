<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ContactUs;
use App\Notifications\AdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContactUsContoller extends Controller
{
    public function contactUs(Request $request) {
        $validator = Validator::make($request->all(), [
            'name'          => 'required|min:3|string',
            'email'         => 'required|email',
            'phone'         => 'required|numeric',
            'message'       => 'required|min:10|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => trans('msg.validation'),
                'errors'    =>  $validator->errors(),
            ], 400);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message
            ];

            $insert = ContactUs::create($data);

            if($insert)
            {
                $admin = Admin::first();

                $user = new \stdClass();
                $user->id    = '';
                $user->fname = $request->input('name');
                $user->lname = '';
                $user->email = $request->input('email');

                $message = [
                    'title' => trans('msg.notification.contact-us-title'),
                    'msg'   => $request->name.' '.trans('msg.notification.contact-us')
                 ];

                 $admin->notify(new AdminNotification($message, $user));
                return response()->json([
                    'status'    => 'success',
                    'message'   =>  trans('msg.add.success'),
                ], 200);
            }else{
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>   trans('msg.add.failed'),
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
