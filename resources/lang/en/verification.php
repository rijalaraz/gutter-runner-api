<?php

/*
|--------------------------------------------------------------------------
| Email verification Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are used during email verification for various
| messages that we need to display to the user. You are free to modify
| these language lines according to your application's requirements.
|
*/

return [
    'sent' => 'An email address verification email has been sent to you.',
    'mustverify' => "You must :linkOpen verify :linkClose your email first.",
    'invalid'   => 'Your email address verification failed.',
    'already_verified' => 'Your email address has already been verified.',
    'verified' => "Your email address is verified.",
    'user' => "No user was found with this email address.",
    'message'   => [
        'greeting'  => 'Hello!',
        'subject'   => 'Verification Gutter Runner App',
        'line1'     => 'Please click the button below to verify your email address.',
        'action'    =>  [
            'text'  =>  "Verify your email address",
            'url'   =>  '',
        ],
        'line2'     => 'If you did not create an account, no further action is required.',
        'salutation' => 'Regards',
    ]
];
