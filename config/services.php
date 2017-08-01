<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mailgun Setup
    |--------------------------------------------------------------------------
    |
    */
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN', 'domain.mailgun.org'),
        'secret' => env('MAILGUN_KEY', 'mailgun_key'),
    ]

];
