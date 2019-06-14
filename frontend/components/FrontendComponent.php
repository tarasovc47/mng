<?php

namespace frontend\components;

use common\components\Base;
use Yii;
use common\models\Access;
class FrontendComponent extends Base
{
	public function beforeAction($action){

		if(!parent::beforeAction($action)){
			return false;
		}

        $checkModule = function ($route) {
            return $route === Yii::$app->controller->module->id;
        };

        $checkController = function ($route) {
            return $route === Yii::$app->controller->id;
        };

        $checkAction = function ($route) {
            return $route === Yii::$app->controller->action->id;
        };
        $checkAccess = function ($module_id) {
            return Access::hasAccess($this->cas_user->id, $this->cas_user->roles, $module_id) ? true : false;
        };

		$this->menuTopItems = [
			[
				'label' => '<img src="/img/avatars/a1.png" style="
    float: left;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    margin-right: 10px;
    margin-top: -2px;" class="img-circle"
                                 alt="User Image"/> ' . $this->cas_user->first_name . ' ' . $this->cas_user->last_name,
				'url' => ['/#'],
				'options' => [ 'class' => 'dropdown' ],
				'items' => [
					['label' => '<i class="fa fa-fw fa-gear"></i> Личный кабинет', 'url' => ['/user/profile/']],
					['label' => '<i class="fa fa-fw fa-question-circle"></i> Справка', 'url' => ['/#']],
            		['label' => '<i class="fa fa-fw fa-sign-out"></i> Выход', 'url' => ['/site/logout/']],
				],
			],
	    ];

	    $this->menuLeftItems = [
            [
                'label' => 'Гидра',
//                'visible' => !Yii::$app->user->isGuest,
                'visible' => 0,
                'icon'=>'users',
                'template' => '<a href="{url}"><i  id="anchor-header"></i>&nbsp;&nbsp;&nbsp;<span>{label}</span>  <i class="fa fa-copyright"></i>  <i class="fa fa-angle-left pull-right"></i></a>',
                'active' => $checkModule('hydra'),
                'items' => [
                    [
                        'label' => 'Абоненты',
                        'active'=>$checkController('hydra/customers'),
                        'icon'=>'users',
                        'items' => [
                            [
                                'label' => 'Физические лица',
                                'active'=>$checkAction('persons'),
                                'icon'=>'users',
                                'url' => '/hydra/customers/persons/',
                            ],
                            [
                                'label' => 'Юридические лица',
                                'active'=>$checkAction('entities'),
                                'url' => '/hydra/customers/entities/',
                            ],
                        ]
                    ],
                ],
            ],
	    	['label' => 'Поиск абонентов', 'icon' => 'search', 'url' => '/client-search'],
            ['visible' => false,'active' => $checkController('urls'),'label' => 'Генератор ссылок', 'icon' => 'rub', 'url' => '/tools/urls'],
	    	['label' => 'Рабочий стол', 'icon' => 'tachometer', 'url' => '/techsup/dashboard/index'],
            ['visible' => false, 'active' => $checkModule('yate'),'label' => 'Кедр', 'icon' => 'tree',
                'items' => [
                    [
                        'label' => 'Мастер номеров',
                        'active' => $checkController('wizard'),
                        'url' => '/yate/wizard',
                        'icon' => 'tty'
                    ],
                    [
                        'label' => 'Настройки абонентов',
                        'active' => $checkController('subscribers'),
                        'url' => '/yate/subscribers',
                        'icon' => 'braille'
                    ],
                    [
                        'label' => 'Маршрутизпция',
                        'active' => $checkController('routing'),
                        'url' => '/yate/routing',
                        'icon' => 'map-o'
                    ],
                    [
                        'label' => 'Профили компаний',
                        'active' => $checkController('companies'),
                        'url' => '/yate/companies',
                        'icon' => 'vcard'
                    ],

                    [
                        'label' => 'Инструменты',
                        'active' => $checkController('tools'),
                        'icon' => 'wrench',
                        'items'=>[
                            [
                                'label' => 'Отчеты',
                                'active' => $checkAction('reports'),
                                'url' => '/yate/tools/reports',
                                'icon' => 'file-zip-o'
                            ],
                            [
                                'label' => 'Проверка маршрутов',
                                'active' => $checkAction('check-routes'),
                                'url' => '/yate/tools/check-routes',
                                'icon' => 'road'
                            ],
                            [
                                'label' => 'Аккаунты',
                                'active' => $checkAction('accounts'),
                                'url' => '/yate/tools/accounts',
                                'icon' => 'user-secret'
                            ],
                            [
                                'label' => 'Мониторинг',
                                'active' => $checkAction('monitoring'),
                                'url' => '/yate/tools/monitoring',
                                'icon' => 'heartbeat'
                            ]
                         ],

                    ]
            ]],
	    	['active' => $checkModule('ipmon'),'label' => 'Мониторинг', 'icon' => 'connectdevelop', 'items' => [
//	    		['label' => 'Сессии', 'icon' => 'users', 'url' => '/radius/pppoe'],
	    		[ 'visible'=>$checkAccess(14),'active' => $checkModule('sessions'),'label' => 'Сессии<sup style="color: wheat"><i>v2</i></sup>', 'icon' => 'users', 'url' => '/sessions', 'encode'=>false],
//	    		['label' => 'Сеть', 'icon' => 'sitemap', 'url' => '/ipmon'],
//	    		['label' => 'ARP таблицы', 'icon' => 'list', 'url' => '/ipmon/arps'],
	    		[ 'visible'=>$checkAccess(9),'active' => $checkController('arptables'),'label' => 'ARP таблицы', 'icon' => 'list', 'url' => '/ipmon/arptables'],
	    		[ 'visible'=>$checkAccess(19),'active' => $checkController('backbone'),'label' => 'Опорная сеть', 'icon' => 'object-ungroup', 'url' => '/ipmon/backbone'],
	    	]],
	    	[ 'active' => $checkModule('tools'),'label' => 'Управление', 'icon' => 'gamepad', 'items' => [
	    	    [ 'active' => $checkController('converter'),'label' => 'Конвертер конфигураций', 'icon' => 'language', 'url' => '/tools/converter'],
	    		[ 'active' => $checkController('swconf'),'label' => 'Автоконфигурирование', 'icon' => 'microchip', 'url' => '/tools/swconf'],
	    		[ 'visible'=>$checkAccess(35),'active' => $checkController('voip'),'label' => 'Сервис VoiceIP', 'icon' => 'phone', 'url' => '/tools/voip'],
	    		[ 'active' => $checkController('raspberry'),'label' => 'Терминальные клиенты', 'icon' => 'terminal', 'url' => '/tools/raspberry'],
	    	]],
	    	['active' => $checkModule('tariffs'),'label' => 'Тарифные планы', 'icon' => 'handshake-o', 'items' => [
	    		['visible'=>$checkAccess(26),'active' => $checkController('tariffs'),'label' => 'Тарифные планы', 'url' => '/tariffs/tariffs/index'],
	    		['visible'=>$checkAccess(26),'active' => $checkController('tariffs-groups'),'label' => 'Группы тарифных планов', 'url' => '/tariffs/tariffs-groups/index'],
	    	]],
	    	['active' => $checkController('manag-companies'),'label' => 'Компании, <br>предоставляющие доступ', 'icon' => 'building-o', 'url' => '/manag-companies/index?ManagCompaniesSearch[publication_status]=1', 'encode'=>false],
	    	['active' => $checkController('contact-faces'),'label' => 'Контактные лица', 'icon' => 'address-book-o', 'url' => '/contact-faces/index?ContactFacesSearch[publication_status]=1'],
	    	['active' => $checkModule('zones'),'label' => 'Зоны присутствия', 'icon' => 'globe', 'items' => [
	    		['visible'=>$checkAccess(23),'active' => $checkController('zones-addresses'),'label' => 'Адреса', 'icon' => 'map-marker', 'url' => '/zones/zones-addresses/index', 'access' => 23],
	    		['visible'=>$checkAccess(24),'active' => $checkController('districts-and-areas'),'label' => 'Округа и районы', 'icon' => 'map-o', 'url' => '/zones/districts-and-areas/index'],
	    		['visible'=>$checkAccess(25),'active' => $checkController('access-agreements'),'label' => 'Договоры доступа', 'icon' => 'book', 'url' => '/zones/access-agreements/index'],
	    		['active' => $checkController('poligons'),'label' => 'Распределение на бригады', 'url' => '/zones/poligons/index'],
	    	]],
	    	['active' => $checkModule('statistics'),'label' => 'Статистика', 'icon' => 'area-chart', 'items' => [
	    		['active' => $checkController('docs-archive'),'label' => 'Архив документов', 'icon' => 'archive ', 'url' => '/statistics/docs-archive/index'],
	    	]],
	    ];

		return true;
	}
}