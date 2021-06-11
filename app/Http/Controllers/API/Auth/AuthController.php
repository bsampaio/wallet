<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Lifepet\Utils\Text;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    const CREDENTIALS_DID_NOT_MATCH = 'Credentials did not match with any user.';
    const SUCCESSFULLY_LOGGED_OUT = 'You have been successfully logged out!';
    const TOKEN_NAME_PASSWORD_GRANT_CLIENT = 'Laravel Password Grant Client';
    const NO_USER_FOUND_WITH_GIVEN_DATA = 'No user can be found with the given data.';

    public function register (Request $request) {
        if($request->nickname) {
            $this->transformNickname($request);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        $user = User::create($request->toArray());
        $tokenResult = $user->createToken(self::TOKEN_NAME_PASSWORD_GRANT_CLIENT);

        $response = $this->formatTokenResponse($tokenResult);

        return response($response, 200);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $this->logoutAll($user);

                $tokenResult = $user->createToken(self::TOKEN_NAME_PASSWORD_GRANT_CLIENT);

                $response = $this->formatTokenResponse($tokenResult);

                return response($response, 200);
            }
        }

        $response = ["message" => self::CREDENTIALS_DID_NOT_MATCH];
        return response($response, 422);
    }

    public function logout (Request $request) {
        $this->logoutAll($request->user());
        $response = ['message' => self::SUCCESSFULLY_LOGGED_OUT];
        return response($response, 200);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function isNicknameAvailable(Request $request) {
        if($request->nickname) {
            $this->transformNickname($request);
        }

        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255|unique:users',
        ]);

        $nickname = $request->nickname;
        $valid = true;
        $errors = [];
        if ($validator->fails()) {
            $valid = false;
            $errors = $validator->errors()->all();
        }

        return [
            'nickname'  => $nickname,
            'valid'     => $valid,
            'errors'    => $errors,
            'available' => $valid ? !User::nickname($request->nickname)->exists() : false,
        ];

    }

    private function logoutAll(User $user)
    {
        /**
         * @var Token $token
         */
        foreach($user->tokens as $token) {
            $token->revoke();
        }

    }

    /**
     * Adapts nickname to lowercase alphanumeric pattern
     * Also remove accents
     * @param Request $request
     */
    private function transformNickname(Request $request)
    {
        $request->nickname = Text::remove_accents($request->nickname);
        $request->nickname = Text::lower($request->nickname);
    }

    /**
     * @param $tokenResult
     * @return array
     */
    public function formatTokenResponse($tokenResult): array
    {
        $response = [
            'type' => 'Bearer',
            'token' => $tokenResult->accessToken,
            'expires_at' => $tokenResult->token->expires_at,
        ];
        return $response;
    }
}
