<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Notifications\DeleteAccountRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use function App\Helpers\AuthUser;

class UserProfileController extends Controller
{
    // By Aaaisha Shaikh
    public function getProfile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id'   => ['required','alpha_dash', Rule::notIn('undefined')],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  __('msg.validation'),
                ],400
            );
        }

        try {
            $user_id = $req->user_id;
            $user  = AuthUser($user_id);

            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.details.inactive'),
               ], 400);
            }

            if (!empty($user)) {
                return response()->json(
                    [
                        'status'    => 'success',
                        'data' => $user,
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
    public function deleteAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id'       => ['required','alpha_dash', Rule::notIn('undefined')],
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
            $user_id = $request->user_id;
            $user  = AuthUser($user_id);
            if (empty($user)) {
                 return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.delete.not-found'),
                ], 400);
            }

            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.details.inactive'),
               ], 400);
            }

            $admin = Admin::first();
            if ($admin) {
                $admin->notify(new DeleteAccountRequest($user));
                return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.delete.email-sent'),
                ], 200);
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.delete.email-failed'),
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
