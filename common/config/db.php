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
            'dsn' => 'pgsql:host=1.1.70.12;port=5555;dbname=cherry',
            'username' => 'cherry',
            'password' => '',
        ],
        'db_radarch' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=10.60.249.6;port=5432;dbname=radiusarch',
            'username' => 'web',
            'password' => 'webparol',
        ],
        'db_sip' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=1.1.70.12;port=5432;dbname=asterisk',
            'username' => 'manga',
            'password' => 'kernelpanic',
        ],
        'db_yate' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=1.1.52.23;port=5434;dbname=yate',
            'username' => 'web_yate',
            'password' => '77web_yate77',
        ],
    ],
];

$db = array_merge(
    $db,
    $db_local
);

return $db;