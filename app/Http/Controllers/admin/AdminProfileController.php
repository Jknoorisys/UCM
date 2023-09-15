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
}
