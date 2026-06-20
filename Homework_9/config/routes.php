<?php

return [
    '' => ['controller' => 'index', 'action' => 'index'],
    'posts' => ['controller' => 'posts', 'action' => 'index'],
    'posts/create' => ['controller' => 'posts', 'action' => 'create'],
    'posts/delete/{id}' => ['controller' => 'posts', 'action' => 'delete'],
    'post/{id}' => ['controller' => 'post', 'action' => 'show'],
    'post/{id}/edit' => ['controller' => 'post', 'action' => 'edit'],
    'post/{id}/save' => ['controller' => 'post', 'action' => 'save'],
    'categories' => ['controller' => 'category', 'action' => 'index'],
    'category/{slug}' => ['controller' => 'category', 'action' => 'bySlug'],
    'login' => ['controller' => 'auth', 'action' => 'login'],
    'register' => ['controller' => 'auth', 'action' => 'register'],
    'logout' => ['controller' => 'auth', 'action' => 'logout'],
    'like/{id}' => ['controller' => 'like', 'action' => 'toggle'],
    'error-handler' => ['controller' => 'errorHandler', 'action' => 'index'],
];
