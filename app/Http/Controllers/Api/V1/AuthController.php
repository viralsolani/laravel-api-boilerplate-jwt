<?php

namespace App\Http\Controllers\Api\V1;

use Auth;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthController extends APIController
{
    /**
     * Log the user in.
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
            return $this->respondInternalError($e->getMessage());
        }

        return $this->respond([
            'message'   => 'You are successfully logged in!',
            'token'     => $token,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();

            if ($token) {
                JWTAuth::invalidate($token);
            }
        } catch (JWTException $e) {
            return $this->respondInternalError($e->getMessage());
        }

        return $this->respond([
            'message'   => 'Successfully logged out',
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $token = JWTAuth::getToken();


        if (!$token) {
            $this->respondUnauthorized('Token not provided');
        }

        try {

            $refreshedToken = JWTAuth::refresh($token);

        } catch (JWTException $e) {
            return $this->respondInternalError($e->getMessage());
        }

        return $this->respond([
            'status' => 'ok',
            'token' => $refreshedToken
        ]);
    }
}
