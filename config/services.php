<?php

return [

    /*
      |--------------------------------------------------------------------------
      | Third Party Services
      |--------------------------------------------------------------------------
      |
      | This file is for storing the credentials for third party services such
      | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
      | default location for this type of information, allowing packages
      | to have a conventional place to find your various credentials.
      |
     */

    'mailgun' => [
        'domain' => 'sandboxf234c767cbbf4e3bb0edf418e2ff36a8.mailgun.org',
        'secret' => 'key-dacfff2de6f003b52000c63e80aa9427',
    ],
    'mandrill' => [
        'secret' => '9pe7b79A9LXqaRjXKFXvQA',
    ],
    'ses' => [
        'key' => '',
        'secret' => '',
        'region' => 'us-east-1',
    ],
    'stripe' => [
        'model' => 'User',
        'secret' => '',
    ],
];
