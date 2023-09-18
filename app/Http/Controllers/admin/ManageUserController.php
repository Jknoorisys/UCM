<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use function App\Helpers\AuthUser;
use App\Models\User;

class ManageUserController extends Controller
{
    public function getUserList(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'page_number'   => 'required||numeric',
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
            $per_page = 10;
            $page_number = $req->input(key:'page_number', default:1);
            $user = DB::table('users')->where('is_verified', '=', 'yes');
            $search = $req->search ? $req->search : '';
            if (!empty($search)) {
                $user->where('fname', 'LIKE', "%$search%");
                $user->orWhere('lname', 'LIKE', "%$search%");
                $user->orWhere('email', 'LIKE', "%$search%");
            }

            $total = $user->count();
            $users = $user->offset(($page_number - 1) * $per_page)
                                    ->limit($per_page)
                                    ->orderBy('fname')
                                    ->get();

            if (!($users->isEmpty())) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.admin.get-users.success'),
                    'total'     => $total,
                    'data'      => $users
                ],200);
            } else {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.admin.get-users.failure'),
                    'data'      => [],
                ],200);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function getUserProfile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => ['required','alpha_dash', Rule::notIn('undefined')],

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

            $user = DB::table('users')->where('id', '=', $req->user_id)->first();
            if(!empty($user))
            {
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

    public function changeStatus(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => ['required','alpha_dash', Rule::notIn('undefined')],

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
            $user_id = $req->user_id;
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

            $user = user::find(1);
            $changeStatusRequest = $user->notify(new UserNotification($user));
            if ($changeStatusRequest) {
                return response()->json([
                        'status'    => 'success',
                        'message'   => trans('msg.delete.email-sent'),
                        'data'      => $user
                ], 200);
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   => trans('msg.delete.email-failed'),
                        'data'      => $user
                ], 200);
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
