<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\UserResource;
use App\Models\User\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Validator;

class UsersController extends APIController
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
     * Return the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return UserResource::collection(
            $this->repositery->getPaginated('1')
        );
    }

    /**
     * Return the specified resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validation = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'email|unique:users,email,'.$user->id,
            'password'  => 'nullable|confirmed',
        ]);

        if ($validation->fails()) {
            return $this->throwValidation($validation->messages()->first());
        }

        $user = $this->repositery->update($user->id, $request->all());

        return new UserResource($user);
    }
}
