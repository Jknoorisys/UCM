<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Message for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'validation' => 'Validation Failed!',

    'localization' => [
        'invalid' => 'Invalid Language Selected!',
        'required' => 'X-localization header is required'
    ],
    
    'jwt' => [
        'TokenNotSet' => 'Bearer Token Not Set',
        'InvalidToken' => 'Invalid Bearer Token',
        'expiredToken' => 'Token Expired!',
        'TokenNotFound' => 'Token Not Found'
    ],

    'registration' => [
        'success' => 'Registration Successful',
        'failed'  => 'Registration Faild',
        'invalid' => 'Unable to Register, please try again...'
    ],

    'login' => [
        'success' => 'Login Successful',
        'failed'  => 'Login Faild',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Password Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it firts...'
    ],

    'reset-password' => [
        'success' => 'Password Reset Successfully',
        'failed'  => 'Unable to Reset Password, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'OTP Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'email-sent' => 'Reset Password OTP Sent on Registered Email'
    ],

    'change-password' => [
        'success' => 'Password Updated Successfully',
        'failed'  => 'Unable to update Password, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Old Password Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it firts...'
    ],

    'add' => [

    ],
    
    'update' => [

    ],

    'list' => [

    ],

    'details' => [

    ],
];
