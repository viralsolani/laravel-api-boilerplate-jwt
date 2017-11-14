<?php

namespace App\Http\Controllers\Api\V1;

use Symfony\Component\HttpKernel\Exception\HttpException;
use JWTAuth;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Auth;
use Validator;

class LoginController extends APIController
{
    /**
     * Log the user in
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $credentials = $request->only(['email', 'password']);

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->throwValidation('Invalid Credentials! Please try again.');
            }
        } catch (JWTException $e) {
            return $this->respondInternalError('This is something wrong. Please try again!');
        }

        return $this->respond([
            'message'   => 'You are successfully logged in!',
            'token'     => $token,
        ]);
    }
}
