<?php

// This file has been auto-generated by the Symfony Routing Component.

return [
    '_preview_error' => [['code', '_format'], ['_controller' => 'error_controller::preview', '_format' => 'html'], ['code' => '\\d+'], [['variable', '.', '[^/]++', '_format', true], ['variable', '/', '\\d+', 'code', true], ['text', '/_error']], [], []],
    'user' => [[], ['_controller' => 'App\\Controller\\UserController::user'], [], [['text', '/user']], [], []],
    'users' => [[], ['_controller' => 'App\\Controller\\UserController::users'], [], [['text', '/users']], [], []],
    'hide_user' => [[], ['_controller' => 'App\\Controller\\UserController::hideUser'], [], [['text', '/hide_user']], [], []],
];
