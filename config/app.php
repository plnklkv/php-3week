<?php

return[
    //кл аут
    'auth' => \Src\Auth\Auth::class,
    //кл польз-ля
    'identity' => \Model\User::class,
    //кл провайдеров
    'providers' => [
        'kernel' => \Providers\KernelProvider::class,
        'route' => \Providers\RouteProvider::class,
        'db' => \Providers\DBProvider::class,
        'auth' => \Providers\AuthProvider::class,
    ],
    //классы для middleware
    'routeMiddleware' => [
        'auth' => \Middleware\AuthMiddleware::class
    ],
    'routeAppMiddleware' => [
        'trim' => \Middleware\TrimMiddleware::class,
        'specialChars' => \Middleware\SpecialCharsMiddleware::class,
        'csrf' => \Middleware\CSRFMiddleware::class,
        'json' => \Middlewares\JSONMiddleware::class
    ],
    'validators' => [
        'required' => \Validators\RequireValidator::class,
        'unique' => \Validators\UniqueValidator::class
    ]
];