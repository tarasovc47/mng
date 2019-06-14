<?php
return [
    'domain' => $_SERVER['HTTP_HOST'],
    'db_billing' => [
    	'host' => '1.1.70.31',
    	'dbname' => 'cherry',
    	'user' => 'cherry',
    	'password' => 'master123'
    ],
    'abonent_types' => [
    	'1' => 'Физическое лицо',
        '2' => 'Юридическое лицо',
    ],
    'zones__room_types' => [
        '1' => 'Квартира',
        '2' => 'Офис',
    ],
    'dadata_keys' => [
        'api_key' => 'c266afe02f675686722c1812ccdb116a9a59e97f',
        'secret_key' => '64363d5b9988ea735bc649d46de6353fabc21e4d',
    ],
];
