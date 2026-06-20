<?php

require __DIR__ . '/config/app.php';

$dataDir = __DIR__ . '/data';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents(__DIR__ . '/database.sql');
    $db->exec($sql);

    $db = null;
    echo "Database initialized successfully.\n";
} catch (Exception $exception) {
    echo $exception->getMessage() . "\n";
    exit(1);
}
