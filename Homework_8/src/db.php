<?php

namespace CompanyName\Blog;

function getDb(): \PDO
{
    static $db = null;

    if (is_null($db)) {
        $dbPath = str_replace('\\', '/', dirname(__DIR__) . '/database.db');
        $db = new \PDO('sqlite:' . $dbPath);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    }

    return $db;
}
