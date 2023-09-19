<?php

namespace App\Helpers;
use App\Models\User;

function AuthUser($user_id) {
    $user  = User::where([['id', '=', $user_id], ['is_verified', '=', 'yes']])->first();

    if (empty($user)) {
            return response()->json([
                'status'    => 'failed',
                'message'   =>  trans('msg.login.not-found'),
        ], 400);
    }

    return $user;
}