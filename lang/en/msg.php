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

    'forgotpassword' => [
        'resetpassword' => 'Need to reset your password?',
        'click' => 'No problem! Just click on the button below and you’ll be on your way.',
        'forgotpassword' => 'Forgot Password',
        'eamilsent' => 'Email Sent Successfully',
        'emailnotsent' => 'Unable to Send Email, Please Try Again',
        'reset' => 'Password Reset Successfuly!',
        'notreset' => 'Unable to reset the password',
        'passnotmatch' => 'Password does not match',
        'otpnotmatch' => 'Incorrect OTP entered',
    ],

    'otp' => [
        'otpver' => 'Registration Successfull!',
        'failure' => 'Unable to Verify OTP, Please Try Again',
        'otpnotver' => 'OTP does not match!',
        
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
