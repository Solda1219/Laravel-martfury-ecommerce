<?php

return [
    'mailchimp' => [
        'api_key' => env('MAILCHIMP_APIKEY'),
        'list_id' => env('MAILCHIMP_LIST_ID'),
    ],
    'sendgrid' => [
        'api_key' => env('SENDGRID_API_KEY'),
        'list_id' => env('SENDGRID_LIST_ID'),
    ],
];
