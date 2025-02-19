<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Your password has been reset!',
    'sent' => 'A reset email has been sent to :email. <i>It could be in your spam folder</i>.',
    'throttled' => 'Please wait before retrying.',
    'token' => 'This password reset token is invalid.',
    'user' => "We can't find a user with that email address.",
    'message'   => [
        'greeting'  => 'Hello!',
        'subject'   => 'Resetting your :app_name password',
        'line1'     => 'To reset your password, click on the link where you will be asked for a new password.',
        'action'    =>  [
            'text'  =>  'Reset',
            'url'   =>  ':app_url/auth/password-reset/:token',
        ],
        'salutation' => 'Regards',
    ]
];
