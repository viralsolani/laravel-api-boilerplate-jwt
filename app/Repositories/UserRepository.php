<?php

namespace App\Repositories;

use App\Models\User\User;
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
     * Create user account.
     *
     * @param array $data
     *
     * @return static
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

        //$user->notify(new UserNeedsConfirmation($user));

        /*
        * Return the user object
        */
        return $user;
    }
}
