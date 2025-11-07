<?php

return [
    // define all modules that can have category trees
    'scopes' => [
        'factory' => ['Default', 'Compliance', 'Quality'],
        'buyer' => ['Default'],
        'bank' => ['Default'],
        'employee' => ['Default'],
    ],
];