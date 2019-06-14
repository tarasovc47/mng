<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'debug', 'gii'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site',
    'components' => [
        /*'view' => [  //надо переделывать header и sidebar
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@vendor/dmstr/yii2-adminlte-asset/example-views/yiisoft/yii2-app'
                ],
            ],
        ],*/
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => 'Zo6gmQbs',

        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'rules' => [
//                '/login'=>'/login/index',
                '/fias/place/find.json' => 'fias/place-find',
                '/fias/place/insert.json' => 'fias/place-insert',
                '/fias/place/select.json' => 'fias/place-select',
                '/fias/fias/select.json' => 'fias/fias-select',
                '/fias/fias/widget.json' => 'fias/fias-widget',
                '/fias/fias/text.json' => 'fias/fias-text',
                '/ipmon/backbone/'=>'/ipmon/backbone/index',
                '/sessions/archive/<page:\d+>'=>'/sessions/archive/index',
                '/sessions/archive'=>'/sessions/archive/index',
                '/sessions/blacklist'=>'/sessions/blacklist/index',
//		'/sessions/accounting'=>'/sessions/accounting/index',


 //               '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>' => '<_m>/<_c>/view',
   //             '<_m:[\w\-]+>/<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => '<_m>/<_c>/<_a>',
     //           '<_m:[\w\-]+>' => '<_m>/default/index',
       //         '<_m:[\w\-]+>/<_c:[\w\-]+>' => '<_m>/<_c>/index',
	//	'/sessions/accounting'=>'/sessions/accounting/index',

                '/sessions/<action>'=>'/sessions/default/<action>',

//                '<controller:\w+>/<action:\w+>/page-<page:\d+>' => '<controller>/<action>',
                '/ipmon/backbone/node/<node:\d+>'=>'/ipmon/backbone/node',
                '/ipmon/backbone/node/<node:\d+>/vlan/<vlan:\d+>'=>'/ipmon/backbone/vlan',
                '/ipmon/backbone/node/<node:\d+>/vlan'=>'/ipmon/backbone/vlan',
                '/ipmon/backbone/node/<node:\d+>/vlan/<vlan:\d+>/<host:\d+>'=>'/ipmon/backbone/host',
                '/ipmon/backbone/editnode/<node:\d+>'=>'/ipmon/backbone/editnode',
                '/ipmon/backbone/validate/<type:(vlan|node|host|lhost)>'=>'/ipmon/backbone/validate',
                '/radius/session/<id:[a-zA-Z0-9_\-\.]+>'=>'/radius/default/session',
                '/ipmon/arptables/<id:\d+>'=>'ipmon/arptables',
                '/ipmon/arptables/<id:\d+>/edit'=>'ipmon/arptables/editrouter',
                '/ipmon/arptables/<id:\d+>/<network:\d+>'=>'ipmon/arptables/network',
//                '/radius/session/<id:\w+>'=>'/radius/default/session',
                '/tools/voip/del/<mac:\w+>/<nport:\d+>'=>'/tools/voip/del',
                '/tools/voip/gate/<mac:\w+>'=>'/tools/voip/gate',

            ],
        ],
    ],
    'modules' => [
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['*'],
        ],
        'techsup' => [
            'class' => 'frontend\modules\techsup\Module',
        ],
        'ipmon' => [
            'class' => 'frontend\modules\ipmon\Module',
        ],
        'tools' => [
            'class' => 'frontend\modules\tools\Module',
        ],
        'zones' => [
            'class' => 'frontend\modules\zones\Module',
        ],
        'telephony' => [
            'class' => 'frontend\modules\telephony\Module',
        ],
        'radius' => [
            'class' => 'frontend\modules\radius\Module',
        ],
        'sessions' => [
            'class' => 'frontend\modules\sessions\Module',
        ],
        'abonent' => [
            'class' => 'frontend\modules\abonent\Module',
        ],
        'statistics' => [
            'class' => 'frontend\modules\statistics\Module',
        ],
        'tariffs' => [
            'class' => 'frontend\modules\tariffs\Module',
        ],
        'yate' => [
            'class' => 'frontend\modules\yate\Module',
        ],
    ],
    'params' => $params,
];
