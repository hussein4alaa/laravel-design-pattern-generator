<?php
return [
    'resources' => [
        'user' => [
            'allowedSorts' => [
              'id',
              'name',
              'email',
            ],
            'allowedFilters' => [
                'id',
                'name',
                'email',
              ],
            'allowedIncludes' => [
              'posts',
            ],
        ],








//dont-remove-or-edit-this-line
    ]
];

?>