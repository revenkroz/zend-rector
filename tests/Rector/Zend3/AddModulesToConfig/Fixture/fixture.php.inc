<?php
return [
    // ...
    'modules' => [
        'ErrorHandler',
        'EventBus',
        'DoctrineModule',
        'DoctrineORMModule',
        'Application',
    ],
    // ...
    'test_data' => [
        'config' => [
            'asd' => 'asd',
            'asd2' => 'asd2',
        ],
    ],
];
?>
-----
<?php
return [
    // ...
    'modules' => ['Zend\I18n', 'Zend\Mvc\Console', 'Zend\Log', 'Zend\Router', 'ErrorHandler', 'EventBus', 'DoctrineModule', 'DoctrineORMModule', 'Application'],
    // ...
    'test_data' => [
        'config' => [
            'asd' => 'asd',
            'asd2' => 'asd2',
        ],
    ],
];
?>
