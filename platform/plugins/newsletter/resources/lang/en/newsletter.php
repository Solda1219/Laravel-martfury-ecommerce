<?php

return [
    'name'     => 'Newsletters',
    'settings' => [
        'email' => [
            'templates' => [
                'title'       => 'Newsletter',
                'description' => 'Config newsletter email templates',
                'to_admin'    => [
                    'title'       => 'Email send to admin',
                    'description' => 'Template for sending email to admin',
                ],
                'to_user'     => [
                    'title'       => 'Email send to user',
                    'description' => 'Template for sending email to subscriber',
                ],
            ],
        ],
    ],
    'statuses' => [
        'subscribed'   => 'Subscribed',
        'unsubscribed' => 'Unsubscribed',
    ],
];
