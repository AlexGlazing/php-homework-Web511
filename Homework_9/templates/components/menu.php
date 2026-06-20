<?php

use function CompanyName\Blog\isActivePath;
use function CompanyName\Blog\Models\getCurrentUser;

$currentUser = getCurrentUser();
?>
<nav>
    <a href="/" class="<?= isActivePath('/') ? 'active' : '' ?>">Главная</a>
    <a href="/posts" class="<?= isActivePath('/posts') || isActivePath('/post') ? 'active' : '' ?>">Посты</a>
    <a href="/categories" class="<?= isActivePath('/categories') || isActivePath('/category') ? 'active' : '' ?>">Категории</a>
</nav>
