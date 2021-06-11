<?php


namespace App\Services;


use App\Exceptions\User\InvalidUserDataReceived;
use App\Models\User;
use Lifepet\Utils\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param Request $request
     * @return mixed
     * @throws InvalidUserDataReceived
     */
    public function makeFromRequest(Request $request, $autoPassword = true)
    {
        if($request->nickname) {
            $this->transformNickname($request);
        }

        if($autoPassword) {
            $request['password'] = $request['password_confirmation'] = $this->generateRandomPassword();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|regex:/^[A-Za-z.-]+$/|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            throw new InvalidUserDataReceived($validator->errors()->all());
        }

        $request['password'] = Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);
        return User::create($request->toArray());
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
     * Generates an alphanumeric random password
     * @return string
     */
    private function generateRandomPassword(): string
    {
        return Text::random(8);
    }
}
