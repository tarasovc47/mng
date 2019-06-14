<?php

$db_local = file_exists(__DIR__ . '/db-local.php') ? require(__DIR__ . '/db-local.php') : [];
$db = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=mng.t72.ru;port=5432;dbname=mng_main',
            'username' => 'monitor',
            'password' => 'Kernelp@n1c',
        ],
        'db_billing' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=1.1.70.31;port=5432;dbname=cherry',
            'username' => 'cherry',
            'password' => 'master123',
        ],
    ],
];

$db = array_merge(
    $db,
    $db_local
);

return $db;