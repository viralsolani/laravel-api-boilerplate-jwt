<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User\User;
use App\Repositories\UserRepository;
use Config;
use Illuminate\Http\Request;
use JWTAuth;
use Validator;

class RegisterController extends APIController
{
    protected $repositery;

    /**
     * __construct.
     *
     * @param $repositery
     */
    public function __construct(UserRepository $repositery)
    {
        $this->repositery = $repositery;
    }

    /**
     * Register User.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name'                  => 'required',
            'email'                 => 'required|email|unique:users',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $user = $this->repositery->create($request->all());

        if (!Config::get('boilerplate.register.release_token')) {
            return $this->respondCreated([
                'message'  => 'You have registered successfully. Please check your email for activation!',
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return $this->respondCreated([
            'message'   => 'You have registered successfully. Please check your email for activation!',
            'token'     => $token,
        ]);
    }
}
