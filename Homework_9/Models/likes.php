<?php

namespace CompanyName\Blog\Models;

use function CompanyName\Blog\getDb;

function getCurrentUserId(): string
{
    if (!empty($_SESSION['user']['id'])) {
        return 'user:' . (int)$_SESSION['user']['id'];
    }

    return $_COOKIE['uid'] ?? 'guest';
}

function getLikeCount(int|string $postId): int
{
    $db = getDb();
    $stmt = $db->prepare('SELECT COUNT(*) as cnt FROM likes WHERE post_id = ?');
    $stmt->execute([(int)$postId]);
    $row = $stmt->fetch();

    return (int)($row['cnt'] ?? 0);
}

function isLiked(int|string $postId): bool
{
    $db = getDb();
    $stmt = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
    $stmt->execute([(int)$postId, getCurrentUserId()]);

    return (bool)$stmt->fetch();
}

function toggleLike(int|string $postId): array
{
    $db = getDb();
    $pid = (int)$postId;
    $uid = getCurrentUserId();

    $check = $db->prepare('SELECT 1 FROM likes WHERE post_id = ? AND user_id = ?');
    $check->execute([$pid, $uid]);
    $exists = (bool)$check->fetch();

    if ($exists) {
        $del = $db->prepare('DELETE FROM likes WHERE post_id = ? AND user_id = ?');
        $del->execute([$pid, $uid]);
        $liked = false;
    } else {
        $ins = $db->prepare('INSERT OR IGNORE INTO likes (post_id, user_id) VALUES (?, ?)');
        $ins->execute([$pid, $uid]);
        $liked = true;
    }

    return [
        'status' => 'success',
        'liked' => $liked,
        'count' => getLikeCount($pid),
    ];
}

function enrichPostsWithLikes(array $posts): array
{
    return array_map(static function (array $post): array {
        $post['like_count'] = getLikeCount($post['id']);
        $post['liked'] = isLiked($post['id']);

        return $post;
    }, $posts);
}

function enrichPostWithLikes(array $post): array
{
    $post['like_count'] = getLikeCount($post['id']);
    $post['liked'] = isLiked($post['id']);

    return $post;
}
