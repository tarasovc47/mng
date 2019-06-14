<?php

namespace backend\components;

use Yii;
use common\components\Base;
use common\models\Access;
use yii\web\ForbiddenHttpException;

class BackendComponent extends Base
{
    public $permission;

	public function beforeAction($action){
		if(!parent::beforeAction($action)){
			return false;
		}

        $exception = Yii::$app->errorHandler->exception;
        if(isset($exception->statusCode) && ($exception->statusCode == 403)){
            $this->layout = "no-access";
            return true;
        }

        $this->permission = Access::hasAccess($this->cas_user->id, $this->cas_user->roles, 2); // 2 - id доступа к админке

        if(!$this->permission){
            throw new ForbiddenHttpException('Нет доступа');
            return false;
        }

		$this->menuTopItems = [
            ['label' => 'Управление', 'url' => ['/'], 'items' => [
                    ['label' => 'Пользователи', 'url' => ['/cas-user/index']],
                    ['label' => 'Отделы компании', 'url' => ['/departments/index']],
                    ['label' => 'Настройка модулей', 'url' => ['/modules-settings']],
                    ['label' => 'Типы документов', 'url' => ['/docs-types/index']],
                    ['label' => 'Операторы', 'url' => ['/operators/index']],
                    '<li class="divider"></li>',
                    ['label' => 'Услуги', 'url' => ['/global-services/index']],
                    ['label' => 'Сервисы', 'url' => ['/services/index']],
                    ['label' => 'Технологии подключения', 'url' => ['/connection-technologies/index']],
                    '<li class="divider"></li>',
                    ['label' => 'Должности контактных лиц', 'url' => ['/contacts-offices/index?ContactsOfficesSearch[publication_status]=1']],
                    ['label' => 'Типы управляющих компаний', 'url' => ['/manag-companies-types/index']],
                ]
            ],            
            ['label' => 'Модуль «Техподдержка»', 'url' => ['/techsup'], 'items' => [
                    ['label' => 'Типы заявок', 'url' => ['/techsup/applications-types/index']],
                    ['label' => 'Статусы заявок', 'url' => ['/techsup/applications-statuses/index']],
                    ['label' => 'Сценарии', 'url' => ['/techsup/scenarios/index']],
                ]
            ],
            ['label' => 'Модуль «Зоны присутствия»', 'url' => ['/zones'], 'items' => [
                    ['label' => 'Типы адресов', 'url' => ['/zones/address-types/index']],
                    ['label' => 'Статусы объектов', 'url' => ['/zones/address-statuses/index']],
                ]
            ],

            ['label' => 'Выход', 'url' => ['/logout'] ],
        ];

		return true;
	}
}
