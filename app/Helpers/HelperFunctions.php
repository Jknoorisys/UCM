<?php

namespace App\Helpers;
use App\Models\User;

function AuthUser($user_id) {
    $user  = User::where([['id', '=', $user_id], ['is_verified', '=', 'yes']])->first();
    return $user;
}