<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Services;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAuthController extends Controller
{
    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password'   => 'required',
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
            $service = new Services();
            $email = $req->email;
            $password = $req->password;
            $admin  = Admin::where('email', '=', $email)->first();

            if(!empty($admin)) 
            {
                if (Hash::check($password,$admin->password)) {
                    $claims = array(
                        'exp'   => Carbon::now()->addDays(1)->timestamp,
                        'uuid'  => $admin->id
                    );

                    $admin->JWT_token = $service->getSignedAccessTokenForUser($admin, $claims);
                    
                        return response()->json(
                            [
                                'status'    => 'success',
                                'data' => $admin,
                                'message'   =>   __('msg.login.success'),
                            ],200);
                }else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  __('msg.login.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  __('msg.login.incmail'),
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
}
