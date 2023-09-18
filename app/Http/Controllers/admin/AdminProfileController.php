<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    public function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admin_id' => 'required',
            'currentpassword' => 'required',
            'newpassword'   => 'required',
            'confirmpassword' => 'required|same:newpassword',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  __('msg.validation'),
                ], 400
            );
        } 

        try {
            
            $currentpassword = $req->currentpassword;
            $newpassword = $req->newpassword;
            $confirmpassword = $req->confirmpassword;
            $admin  = Admin::where('id', '=', $req->admin_id)->first();

            if(!empty($admin)) 
            {
                if (Hash::check($currentpassword,$admin->password)) {
                    $admin->password = Hash::make($newpassword);
                    $admin->save();
                        return response()->json(
                            [
                                'status'    => 'success',
                                'data' => $admin,
                                'message'   =>   __('msg.change-password.success'),
                            ], 200);
                }else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  __('msg.change-password.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  __('msg.change-password.not-found'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getProfile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'admin_id'   => ['required','alpha_dash', Rule::notIn('undefined')],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  __('msg.validation'),
                ],400);
        }

        try {
            $admin = DB::table('admins')->where('id', '=', $req->admin_id)->first();
            if (!empty($admin)) {
                return response()->json(
                    [
                        'status'    => 'success',
                        'data' => $admin,
                        'message'   =>  __('msg.details.success'),
                    ],200);
            } else {
                return response()->json(
                    [
                        'status'    => 'failed',
                        'message'   =>  __('msg.details.not-found'),
                    ],400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // By Javeriya Kauser
    public function updateBudgetPercentage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'percent'   => ['required', Rule::notIn('undefined')],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'errors'    =>  $validator->errors(),
                'message'   =>  trans('msg.validation'),
            ], 400);
        }

        try {
            $admin = Admin::where('id', '=', $request->admin_id)->first();
            if (empty($admin)) {
                return response()->json([
                    'status'    => 'success',
                    'message'   =>  trans('msg.update.not-found'),
                ], 200);
            }

            $admin->percent = $request->percent;
            $update = $admin->save();

            if ($update) {
                $adminDetails = Admin::where('id', '=', $request->admin_id)->first();
                return response()->json([
                    'status'    => 'success',
                    'data'      => $adminDetails,
                    'message'   =>  trans('msg.update.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.update.failed'),
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

    public function getNotifications(Request $request) {
        $validator = Validator::make($request->all(), [
            'page_number'   => 'required||numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  trans('msg.validation'),
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
}
