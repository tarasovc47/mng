<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language' => 'ru',
    'timeZone' => 'Asia/Yekaterinburg',
    'components' => [
        'ad' => require "ldap.php",
        'assetManager' => [
            'linkAssets' => true,
        ], 
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
    ],
];

