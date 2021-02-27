<?php

return [
    [
        'name' => 'Faq Categories',
        'flag' => 'faq_category.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'faq_category.create',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'faq_category.edit',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'faq_category.destroy',
        'parent_flag' => 'faq_category.index',
    ],
    [
        'name' => 'Faq',
        'flag' => 'faq.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'faq.create',
        'parent_flag' => 'faq.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'faq.edit',
        'parent_flag' => 'faq.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'faq.destroy',
        'parent_flag' => 'faq.index',
    ],
];
