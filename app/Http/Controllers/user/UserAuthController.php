<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Libraries\Services;
use App\Notifications\AdminNotification;

class UserAuthController extends Controller
{
    // By Aaisha Shaikh
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'fname'     => 'required|min:3|string',
            'lname'     => 'required|min:3|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|max:20||min:8',
            'phone'   => 'required|numeric|unique:users',
        ]);

        $errors = [];
        foreach ($validator->errors()->messages() as $key => $value) {
            // if($key == 'email')
                $key = 'error_message';
                $errors[$key] = is_array($value) ? implode(',', $value) : $value;
        }

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => $errors['error_message'] ? $errors['error_message'] : trans('msg.validation'),
                'errors'    => $validator->errors()
            ], 400);
        }

        try {
            $result = DB::table('users')->where('email', $req->input('email'))->get();

            if (!empty($result)) {
                $otp = rand(100000, 999999);
                $data = $req->input();

                $user = [
                    'fname' => $data['fname'], 
                    'lname' => $data['lname'], 
                    'password' => Hash::make($data['password']),
                    'email' => $data['email'], 
                    'phone' => $data['phone'], 
                    'otp' => $otp, 
                    'created_at' => Carbon::now()
                ];

                $saveUser = User::create($user);

                $data = [
                    'salutation' => trans('msg.email.Dear'),
                    'fname'=> $req->fname,
                    'otp'=> $otp, 
                    'msg'=> trans('msg.email.registerus'), 
                    'otp_msg'=> trans('msg.email.otp_msg')
                ];
                $email =  ['to'=> $req->email];
                // echo json_encode($email['to']);exit;
                $datamail = Mail::send('otpmail', $data, function ($message) use ($email) {
                    $message->to($email['to']);
                    $message->subject(__('msg.email.mailverification'));
                });

                if ($saveUser) {
                    $userdata = User::where('email',$user['email'])->first();
                    return response()->json([
                        'status'    => 'success',
                        'message'   => __('msg.registration.email-sent'),
                        'data' => $userdata,
                    ], 200);
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => __('msg.registration.failed'),
                    ], 400);
                }
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOTP(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email_otp'   => 'required',
            'id'          => ['required','alpha_dash', Rule::notIn('undefined')]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'errors'    =>  $validator->errors(),
                'message'   => trans('msg.validation'),
            ], 400);
        }

        try {
            $otp = $req->email_otp;
            $id = $req->id;
            $match_otp = DB::table('users')->where('id', '=', $id)->where('otp', '=', $otp)->first();
            if(!empty($match_otp))
            {
                $verificationCode   =  DB::table('users')->where('otp', '=', $otp)->where('id', '=', $id)->update(['is_verified' => 'yes', 'updated_at' => Carbon::now()]);
                if ($verificationCode) {
                     $user = User::find($id);

                     $message = [
                        'title' => trans('msg.notification.registration-title'),
                        'msg'   => $user->fname.' '.$user->lname.' '.trans('msg.notification.registration')
                     ];

                     $user->notify(new AdminNotification($message, $user));
                    return response()->json([
                        'status'    => 'success',
                        'message'   =>  trans('msg.registration.success'),
                    ], 200);
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   =>   trans('msg.registration.failed'),
                    ], 400);
                }
            }else{
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>   trans('msg.registration.invalid'),
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

    public function resendRegOTP(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email'   => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  trans('msg.validation'),
            ], 400);
        }

        try {
            $email = $req->email;
            $user = User::where('email', '=', $email)->first();

            if (!empty($user)) {
                if ($user->is_verified == 'no') {

                    $email_otp = rand(100000, 999999);
                    $resend =  User::where('email', '=', $email)->update(['otp' => $email_otp, 'updated_at' => date('Y-m-d H:i:s')]);
                    if ($resend == true) {
                        $user = User::where('email', '=', $email)->first();
                        $data = [
                            'salutation' => trans('msg.email.Dear'),
                            'fname'=> $user->fname,
                            'otp'=> $email_otp, 
                            'msg'=> trans('msg.email.Let’s get you Registered with us!'), 
                            'otp_msg'=> trans('msg.email.Your One time Password to Complete your Registrations is')
                        ];
        
                        $email =  ['to'=> $req->email];
                        Mail::send('otpmail', $data, function ($message) use ($email) {
                            $message->to($email['to']);
                            $message->subject(trans('msg.email.mailverification'));
                        });

                        return response()->json([
                            'status'    => 'success',
                            'message'   =>  trans('msg.registration.email-sent'),
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   =>   trans('msg.registration.verified'),
                    ], 400);
                }
            } else {
                return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.registration.not-found'),
                ], 400);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

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
                    'message'   =>  trans('msg.validation'),
                ], 400
            );
        } 

        try {
            $service = new Services();
            $email = $req->email;
            $password = $req->password;
            $user  = User::where('email', '=', $email)->first();

            if(!empty($user)) 
            {
                if (Hash::check($password,$user->password)) {
                    if ($user->status == 'active') {
                        $claims = array(
                            'exp'   => Carbon::now()->addDays(1)->timestamp,
                            'uuid'  => $user->id
                        );

                        $token = $service->getSignedAccessTokenForUser($user, $claims);

                        $user_id  = DB::table('users')->where('email', $email)->where('password', $user->password)->take(1)->first();
                        $user_id->JWT_token = $token;
                        $userJWToken = user::where('id','=',$user->id)->update(['JWT_token' => $user->token]);
                        return response()->json(
                            [
                                'status'    => 'success',
                                'message'   =>   trans('msg.login.success'),
                                'data'      => $user_id,
                            ],200);
                    } else {
                        return response()->json(
                            [
                                'status'    => 'failed',
                                'message'   =>  trans('msg.login.inactive'),
                            ],400);
                    }
                }else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.login.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.login.not-found'),
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

    public function forgetpassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email'   => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'errors'    =>  $validator->errors(),
                'message'   =>  trans('msg.validation'),
            ], 400 );
        }

        try {

            $email = $req->email;
            $user = User::where('email', '=', $email)->first();
            if (!empty($user)) {
                $otp = rand(100000, 999999);
                $otpforgot = [
                    'email' => $user->email,
                    'token' => $otp,
                    'created_at' => Carbon::now()
                ];
                $userforgotpass = DB::table('password_reset_tokens')->insert($otpforgot);
                $data = [
                    'salutation' => trans('msg.email.Dear'),
                    'fname'=> $user->fname,
                    'otp'=> $otp, 
                    'msg'=> trans('msg.email.registerus'), 
                    'otp_msg'=> trans('msg.email.otp_msg')
                ];
                $email =  ['to'=> $req->email];
                // echo json_encode($email['to']);exit;
                $datamail = Mail::send('forgotpassmail', $data, function ($message) use ($email) {
                    $message->to($email['to']);
                    $message->subject(trans('msg.email.mailverification'));
                });

                if ($userforgotpass) {
                    $user = User::where('email', '=', $req->email)->first();
                    $user->forgetpassOTP = $otpforgot['token'];
                    return response()->json([
                            'status'    => 'success',
                            'data' => $user,
                            'message'   =>  trans('msg.reset-password.email-sent'),
                        ], 200);
                } else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.reset-password.failed'),
                        ], 400 );
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.reset-password.not-found'),
                    ], 400 );
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' =>  trans('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function forgotPasswordValidate(Request $req)
    {
        $validator = Validator::make($req->all(), [
            
            'otp' => 'required',
            'password'   => 'required|max:20||min:8',
            'confirm_password' => 'required|same:password',
        ]);

        $errors = [];
        foreach ($validator->errors()->messages() as $key => $value) {
                $key = 'error_message';
                $errors[$key] = is_array($value) ? implode(',', $value) : $value;
        }

        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'errors'    =>  $validator->errors(),
                'message'   =>  $errors['error_message'] ? $errors['error_message'] : __('msg.user.validation.fail'),
            ], 400 );
        }

        try {
            $forgotpassUser = DB::table('password_reset_tokens')->where('token',$req->otp)->first();
            if ($forgotpassUser) {

                $password = $req->password;
                if ($password == $req->confirm_password) {
                    $updatedpassword = Hash::make($req->password);
                    $otp = DB::table('password_reset_tokens')->where('token',$req->otp)->delete();
                    $info = DB::table('users')->where('email', $forgotpassUser->email)->update(['password' => $updatedpassword]);
                    if ($info && $otp) {
                        return response()->json([
                                'status'    => 'success',
                                'message'   =>  trans('msg.reset-password.success'),
                            ], 200);
                    } else {
                        return response()->json([
                                'status'    => 'failed',
                                'message'   =>  trans('msg.reset-password.failed'),
                            ],400
                        );
                    }
                } else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.reset-password.failed'),
                        ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.reset-password.invalid'),
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

    public function changePassword(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'user_id' => 'required',
            'currentpassword' => 'required',
            'newpassword'   => 'required',
            'confirmpassword' => 'required|same:newpassword',
        ]);

        if ($validator->fails()) {
            return response()->json([
                    'status'    => 'failed',
                    'errors'    =>  $validator->errors(),
                    'message'   =>  trans('msg.validation'),
                ], 400);
        } 

        try {
            
            $currentpassword = $req->currentpassword;
            $newpassword = $req->newpassword;
            $confirmpassword = $req->confirmpassword;
            $user  = user::where('id', '=', $req->user_id)->first();

            if(!empty($user)) 
            {
                if (Hash::check($currentpassword,$user->password)) {
                    $user->password = Hash::make($newpassword);
                    $user->save();
                        return response()->json(
                            [
                                'status'    => 'success',
                                'data' => $user,
                                'message'   =>   trans('msg.change-password.success'),
                            ],200);
                }else {
                    return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.change-password.invalid'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.change-password.not-found'),
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
    public function socialRegistration(Request $request) {
        $validator = Validator::make($request->all(), [
            'fname'         => 'required|min:3|string',
            'lname'         => 'required|min:3|string',
            'email'         => 'required|email|unique:users',
            'social_type'   => ['required', Rule::in(['google','facebook','apple','manual'])],
            'social_id'     => 'required',
            'phone'         => 'required|numeric|unique:users',
        ]);

        $errors = [];
        foreach ($validator->errors()->messages() as $key => $value) {
            // if($key == 'email')
                $key = 'error_message';
                $errors[$key] = is_array($value) ? implode(',', $value) : $value;
        }
        
        if ($validator->fails()) {
            return response()->json([
                'status'    => 'failed',
                'message'   => $errors['error_message'] ? $errors['error_message'] : __('msg.validation'),
                'errors'    => $validator->errors()
            ], 400);
        }

        try {
            $otp = rand(100000, 999999);

            $user = [
                'fname'       => $request->fname, 
                'lname'       => $request->lname, 
                'is_social'   => '1',
                'social_type' => $request->social_type,
                'social_id'   => $request->social_id,
                'email'       => $request->email, 
                'phone'       => $request->phone, 
                'otp'         => $otp, 
                'created_at'  => Carbon::now()
            ];

            $create = User::create($user);

            $data = [
                'salutation' => trans('msg.email.Dear'),
                'fname'=> $request->fname,
                'otp'=> $otp, 
                'msg'=> trans('msg.email.registerus'), 
                'otp_msg'=> trans('msg.email.otp_msg')
            ];
                $email =  ['to'=> $request->email];

                $sendEmail = Mail::send('otpmail', $data, function ($message) use ($email) {
                    $message->to($email['to']);
                    $message->subject(__('msg.email.mailverification'));
                });

                if ($create) {

                    $userdata = User::where('email', $user['email'])->first();

                    return response()->json([
                        'status'    => 'success',
                        'message'   => __('msg.registration.email-sent'),
                        'data' => $userdata,
                    ], 200);
                } else {
                    return response()->json([
                        'status'    => 'failed',
                        'message'   => __('msg.registration.failed'),
                    ], 400);
                }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'failed',
                'message' => __('msg.error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    function socialLogin(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'social_type'   => ['required', Rule::in(['google','facebook','apple','manual'])],
            'social_id'     => 'required',
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
            $service = new Services();
            $email = $request->email;
            $social_type = $request->social_type;
            $social_id = $request->social_id;
            $user  = User::where('email', '=', $email)->first();

            if ($user->is_social != '1') {
                 return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.login.not-social'),
                ], 400);
            }

            if (!empty($user)) {
                if (($social_type == $user->social_type) && ($social_id == $user->social_id)) {
                    if ($user->status != 'active') {
                        return response()->json([
                               'status'    => 'failed',
                               'message'   =>  trans('msg.login.inactive'),
                       ], 400);
                    }

                    $claims = array(
                        'exp'   => Carbon::now()->addDays(1)->timestamp,
                        'uuid'  => $user->id
                    );

                    $user->JWT_token = $service->getSignedAccessTokenForUser($user, $claims);
                    $user->save();

                    return response()->json([
                            'status'    => 'success',
                            'message'   => trans('msg.login.success'),
                            'data'      => $user
                    ], 200);
                } else {
                     return response()->json([
                            'status'    => 'failed',
                            'message'   =>  trans('msg.login.invalid-social'),
                    ], 400);
                }
            } else {
                return response()->json([
                        'status'    => 'failed',
                        'message'   =>  trans('msg.login.not-found'),
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
}
