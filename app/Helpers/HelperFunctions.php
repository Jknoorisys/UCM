<?php

    namespace App\Helpers;

    use App\Models\SnapchatTokens;
    use App\Models\User;
    use GuzzleHttp\Client;

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

    function snapchatRefreshToken($user_id) {
    
        $client = new Client();

        $user = AuthUser($user_id);
        if (!empty($user) && $user->status != 'active') {
            return response()->json([
                    'status'    => 'failed',
                    'message'   =>  trans('msg.login.inactive'),
            ], 400);
        }

        $tokens = $user->snapchatToken;

        $data = [
            'grant_type' => 'refresh_token',
            'client_id' => $tokens->client_id,
            'client_secret' => $tokens->client_secret,
            'refresh_token' => $tokens->refresh_token,
        ];

        // Send a POST request to the Snapchat OAuth 2.0 endpoint
        $response = $client->post('https://accounts.snapchat.com/login/oauth2/access_token', [
            'form_params' => $data
        ]);

        // Get the response body as JSON
        $responseBody = (string) $response->getBody();
        $responseData = json_decode($responseBody, true); 

        if ($responseData) {
            
            $snap_tokens = [
                'access_token' => $responseData['access_token'],
                'refresh_token' => $responseData['refresh_token'],
                'status'        => 'linked'
            ];

            $update = SnapchatTokens::where('user_id', '=', $user_id)->update($snap_tokens);

            return $responseData;
        } else {
            return false;
        }
    }