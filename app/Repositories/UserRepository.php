<?php

namespace App\Repositories;

use App\Models\User\User;
use Illuminate\Support\Str;
use App\Notifications\UserNeedsConfirmation;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    /**
     * [__construct description].
     */
    public function __construct()
    {
    }

    /**
     * Create user.
     *
     * @param array $data
     *
     * @return user object
     */
    public function create(array $data)
    {
        $user = new User();

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);

        \DB::transaction(function () use ($user) {
            $user->save();
        });

        $user->notify(new UserNeedsConfirmation($user));

        return $user;
    }

    /**
     * Update user.
     *
     * @param array $data
     *
     * @return user object
     */
    public function update($id, $data)
    {
        $model = $this->find($id);

        $user = \DB::transaction(function () use ($model, $data) {
            return tap($model)->update($data);
        });

        return $user;
    }

    /**
     * Get User By Email
     *
     * @param  request
     * @return user
     */
    public function getUserByEmail($request)
    {
        return $this->query()->where('email', '=', $request->get('email'))->first();
    }

    /**
     * Create a new token for the user.
     *
     * @return string
     */
    public function createNewToken()
    {
        $token = hash_hmac('sha256', Str::random(40), 'hashKey');

        \DB::table('password_resets')->insert([
            'email' => request('email'),
            'token' => $token,
        ]);

        return $token;
    }
}
