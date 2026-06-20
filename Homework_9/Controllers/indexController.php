<?php

namespace CompanyName\Blog\Controllers;

use function CompanyName\Blog\render;
use function CompanyName\Blog\Models\getCurrentUser;

function indexController(string $action, array $params = []): void
{
    echo render('index', [
        'currentUser' => getCurrentUser(),
        'titleSite' => 'Главная',
    ]);
}
