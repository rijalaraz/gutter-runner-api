<?php

namespace App\Exceptions;

use Illuminate\Validation\ValidationException;

class VerifyEmailException extends ValidationException
{
    /**
     * @param  \App\User $user
     * @return static
     */
    public static function forUser($user)
    {
        return static::withMessages([
            'message' => [trans('verification.mustverify', [
                'linkOpen' => '<a href="/email/resend?email='.urlencode($user->email).'">',
                'linkClose' => '</a>',
            ])],
        ]);
    }
}
