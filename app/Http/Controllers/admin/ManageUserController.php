<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use function App\Helpers\AuthUser;

class ManageUserController extends Controller
{
    //By Aaisha Shaikh
    public function getUserList(Request $req)
    {
        $validator = Validator::make($req->all(), [
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
            $page_number = $req->input(key: 'page_number', default: 1);

            $user  = User::where('is_verified', '=', 'yes');
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
                    'message'   => trans('msg.list.success'),
                    'total'     => $total,
                    'data'      => $users
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.list.failed'),
                    'data'      => [],
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

    public function getUserProfile(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => ['required', 'alpha_dash', Rule::notIn('undefined')],
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

            $user_id = $req->user_id;
            $user  = AuthUser($user_id);

            if (!empty($user)) {
                return response()->json([
                    'status'    => 'success',
                    'data' => $user,
                    'message'   =>  trans('msg.details.success'),
                ],200);
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.details.not-found'),
                    ],400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function userStatusChange(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => ['required', 'alpha_dash', Rule::notIn('undefined')],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   =>  trans('msg.validation'),
                'errors'    =>  $validator->errors(),
            ], 400);
        } 

        try {
            $user_id = $req->user_id;
            $status = $req->status;
            $user  = AuthUser($user_id);
            
            $user->status = $status;
            $update = $user->save();

            if ($update) {
                if ($status == 'active') {
                    $message = [
                        'title' => 'Account Activated',
                        'msg' => 'Your account has been activated by Admin',
                    ];
                } else {
                    $message = [
                        'title' => 'Account Deactivated',
                        'msg' => 'Your account has been deactivated by Admin',
                    ];
                }
                
                $user->notify(new UserNotification($message));

                return response()->json([
                    'status'    => 'success',
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

    // By Javeriya Kauser
    public function userDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => ['required', 'alpha_dash', Rule::notIn('undefined')],
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

            $delete = $user->forceDelete();
            if ($delete) {
                return response()->json([
                    'status'    => 'success',
                    'message'   => trans('msg.delete.success'),
                ], 200);
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   => trans('msg.delete.failed'),
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
