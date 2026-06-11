<?php
$currentPage = $_GET['page'] ?? 'index';
?>
<nav>
    <a href="/" class="<?= ($currentPage === 'index') ? 'active' : '' ?>">Главная</a>
    <a href="/?page=posts" class="<?= ($currentPage === 'posts' || $currentPage === 'post' || $currentPage === 'post-create' || $currentPage === 'post-edit') ? 'active' : '' ?>">Посты</a>
    <a href="/?page=categories" class="<?= ($currentPage === 'categories' || $currentPage === 'posts-category') ? 'active' : '' ?>">Категории</a>
</nav>
