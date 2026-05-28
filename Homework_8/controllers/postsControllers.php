<?php

//Вызывается в index динамически через $page . 'Controller'()
use function CompanyName\Blog\getPost;
use function CompanyName\Blog\getPosts;
use function CompanyName\Blog\render;

function postController()
{
    $id = $_GET['id'];
    //валидация
    $post = getPost($id);
    //case: posts
    echo render('post', ['post' => $post]);

}

function postsController()
{

    //валидация
    $post = getPosts($id);
    //case: posts
    echo render('posts', ['posts' => $post]);
}