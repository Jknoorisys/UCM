<?php

namespace App\Http\Controllers\user\snapchat;

use App\Http\Controllers\Controller;
use App\Models\SnapchatTokens;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function App\Helpers\AuthUser;

class AuthController extends Controller
{
    public function authorizeAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id'           => ['required','alpha_dash', Rule::notIn('undefined')],
            'organization_id'   => ['required','alpha_dash', Rule::notIn('undefined')],
            'adaccount_id'      => ['required','alpha_dash', Rule::notIn('undefined')],
            'client_id'         => ['required','alpha_dash', Rule::notIn('undefined')],
            'client_secret'     => ['required','alpha_dash', Rule::notIn('undefined')],
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
            $user_id           = $request->user_id;
            $organization_id   = $request->organization_id;
            $adaccount_id      = $request->adaccount_id;
            $client_id         = $request->client_id;
            $client_secret     = $request->client_secret;

            $user = AuthUser($user_id);
            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.login.inactive'),
               ], 400);
            }

            $params = [
                'client_id' => $client_id,
                'redirect_uri' => env('REDIRECT_URI'),
                'response_type' => 'code',
                'scope' => 'snapchat-marketing-api',
            ];
            
            $query_params = http_build_query($params);
            // Send a POST request to the Snapchat OAuth 2.0 endpoint
            $response = Http::get('https://accounts.snapchat.com/login/oauth2/authorize?'. $query_params);

            $snap_tokens = [
                'user_id' => $user_id,
                'organization_id' => $organization_id,
                'adaccount_id'    => $adaccount_id,
                'client_id'       => $client_id,
                'client_secret'   => $client_secret,
                'status'          => 'inprogress'
            ];

            $create = SnapchatTokens::updateOrCreate(
                ['user_id' => $user_id], 
                $snap_tokens          
            );
            
            // Get the response body as JSON
            if ($create) {
                $url = 'https://accounts.snapchat.com/login/oauth2/authorize?'. $query_params;

                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.auth.success'),
                       'url'       => $url
               ], 400);
            } else {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.auth.failed'),
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

    public function generateToken(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id'           => ['required','alpha_dash', Rule::notIn('undefined')],
            'auth_code'   => ['required','alpha_dash', Rule::notIn('undefined')],
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
            $user_id     = $request->user_id;
            $auth_code   = $request->auth_code;

            $user = AuthUser($user_id);
            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.login.inactive'),
               ], 400);
            }

            $tokens = $user->snapchatToken;

            $data = [
                'grant_type' => 'authorization_code',
                'client_id' => $tokens->client_id,
                'client_secret' => $tokens->client_secret,
                'code' => $auth_code,
                'redirect_uri' => env('REDIRECT_URI'),
            ];
            
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];

            // Send a POST request to the Snapchat OAuth 2.0 endpoint
            $response = Http::withHeaders($headers)->post('https://accounts.snapchat.com/login/oauth2/access_token', $data);
            
            $responseData = ($response->getBody());
            return $responseData;

            // Get the response body as JSON
            if ($response) {
                
                $snap_tokens = [
                    'auth_code' => $auth_code,
                    'access_token' => ''
                ];

                $update = SnapchatTokens::where('user_id', '=', $user_id)->update($snap_tokens);

                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.auth.success'),
                       'data'      => $response
               ], 400);
            } else {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.auth.failed'),
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
