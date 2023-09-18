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

    'error' => 'Something went wrong, please try again ...',

    'email' => [
        'mailverification' => 'Email Verification',
        'Dear' => 'Dear',
        'registerus' => 'Registered with us!',
        'otp_msg' => 'Your One time Password to Complete your Registrations is',
        'resendotp' => 'OTP Resent successfuly',
    ],

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
        'email-sent' => 'Registration OTP Sent on Registered Email',
        'failed'  => 'Registration Faild',
        'invalid' => 'OTP does Not Match, please try again...',
        'verified' => 'Email Already Verified',
        'not-found' => 'User Not Found, Please Register First...',
    ],

    'login' => [
        'success' => 'Login Successful',
        'failed'  => 'Login Faild',
        'not-found' => 'User Not Found, Please Register First...',
        'invalid' => 'Password Does Not Match!',
        'inactive' => 'Account blocked by Admin',
        'not-verified' => 'Email not Verified, please verify it firts...',
        'not-social' => 'Unable to Find Social Account',
        'invalid-social' => 'Social Id Does Not Match, Please try again...',
        'incmail' => 'Invalid Email Address',
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
        'success' => 'Details Added Successully',
        'failed'  => 'Unable to Add, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
    ],
    
    'update' => [
        'success' => 'Details Updated Successully',
        'failed'  => 'Unable to Update, please try again...',
        'not-found' => 'User Not Found, Please Register First...',
    ],

    'list' => [
        'success' => 'List Fetched Successully',
        'failed'  => 'No Data Found',
        'not-found' => 'User Not Found, Please Register First...',
    ],

    'details' => [
        'success' => 'Details Fetched Successully',
        'failed'  => 'No Details Found',
        'not-found' => 'User Not Found, Please Register First...',
    ],
    
    'admin' => [
        'get-users' => [
            'success' => 'User List',
            'failure' => 'No User Found',
        ],
    ],
];
