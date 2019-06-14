<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'pgsql:host=mng.t72.ru;port=5432;dbname=mng72',
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