<?php

namespace App\Http\Controllers\user\tiktok;

use App\Http\Controllers\Controller;
use App\Models\SnapchatTokens;
use App\Models\TiktokTokens;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use function App\Helpers\AuthUser;
use function App\Helpers\snapchatRefreshToken;

class AuthController extends Controller
{
    public function authorizeAccount(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id'           => ['required','alpha_dash', Rule::notIn('undefined')],
            'advertiser_id'     => ['required', Rule::notIn('undefined')],
            'auth_url'          => ['required', Rule::notIn('undefined')],
            'app_id'            => ['required', Rule::notIn('undefined')],
            'secret'            => ['required', Rule::notIn('undefined')],
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
            $advertiser_id     = $request->advertiser_id;
            $auth_url          = $request->auth_url;
            $app_id            = $request->app_id;
            $secret            = $request->secret;

            $user = AuthUser($user_id);
            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.login.inactive'),
               ], 400);
            }

            $tiktok_tokens = [
                'user_id' => $user_id,
                'advertiser_id'   => $advertiser_id,
                'auth_url'        => $auth_url,
                'app_id'          => $app_id,
                'secret'          => $secret,
                'status'          => 'inprogress'
            ];

            $create = TiktokTokens::updateOrCreate(
                ['user_id' => $user_id], 
                $tiktok_tokens          
            );
            
            // Get the response body as JSON
            if ($create) {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.auth.success'),
                       'url'       => $auth_url
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
            $client = new Client();

            $user_id     = $request->user_id;
            $auth_code   = $request->auth_code;

            $user = AuthUser($user_id);
            if (!empty($user) && $user->status != 'active') {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.login.inactive'),
               ], 400);
            }

            $tokens = $user->tiktokToken;
            if (!empty($tokens) && (!empty($tokens->auth_code) && $tokens->auth_code == $auth_code)) {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.token.invalid'),
               ], 400);
            }

            // Prepare the request parameters
            $data = [
                'secret' => $tokens->secret,
                'app_id' => $tokens->app_id,
                'auth_code' => $auth_code,
            ];

            // Send a POST request to the Snapchat OAuth 2.0 endpoint
            $response = Http::post('https://business-api.tiktok.com/open_api/v1.3/oauth2/access_token/', $data);

            // Get the response body as JSON
            $responseBody = (string) $response->getBody();
            $responseData = json_decode($responseBody, true); 

            if ($responseData) {
                
                $tiktok_tokens = [
                    'auth_code' => $auth_code,
                    'access_token' => $responseData['access_token'],
                    'status'        => 'linked'
                ];

                $update = TiktokTokens::where('user_id', '=', $user_id)->update($tiktok_tokens);

                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.token.success'),
                       'data'      => $responseData
               ], 400);
            } else {
                return response()->json([
                       'status'    => 'failed',
                       'message'   =>  trans('msg.token.failed'),
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
