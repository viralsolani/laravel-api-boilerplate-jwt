<?php

namespace App\Http\Controllers\Api\V1;

use Config;
use Validator;
use App\Models\User\User;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RegisterController extends APIController
{
    protected $repositery;

    /**
     * __construct
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

        /*$user = new User($request->all());
        if(!$user->save()) {
            throw new HttpException(500);
        }*/

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return $this->respondCreated([
                'You have registered successfully. Please check your email for activation!',
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return $this->respondCreatedWithData([
            'message'   => 'You have registered successfully. Please check your email for activation!',
            'token'     => $token
        ]);
    }
}
