<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Json;
use common\models\SearchFields;
use common\models\SearchFieldsSettings;
use frontend\components\FrontendComponent;

class UserController extends FrontendComponent
{
	private $userLeftMenu;

	public function beforeAction($action){
		$items = [
					['label' => 'Профиль', 'url' => '/user/profile'],
					['label' => 'Настройки поиска', 'url' => '/user/search-settings']
				];
		$currentUrl = '/'.\Yii::$app->controller->id.'/'.\Yii::$app->controller->action->id;
		$this->userLeftMenu = $this->renderPartial('_user-left-menu', ['items' => $items, 'currentUrl' => $currentUrl]);
		return parent::beforeAction($action);
	}

    public function actionProfile()
    {	
        return $this->render('profile', ['userLeftMenu' => $this->userLeftMenu]);
    }

    public function actionSearchSettings(){
    	$fields = SearchFields::getFieldsList();
        $fields_values = SearchFieldsSettings::getValuesList(0, $this->cas_user->id);

        if (!isset($user_fields_values) || empty($user_fields_values)) {
            $fields_values = SearchFieldsSettings::getValuesList($this->cas_user->department_id, 0);
        }

    	return $this->render('search-settings', 
                                [
                                    'userLeftMenu' => $this->userLeftMenu, 
                                    'fields' => $fields,
                                    'fields_values' => $fields_values,
                                ]
                            );
    }

    public function actionSearchSettingsUpdate()
    {
        $settings = Yii::$app->request->post("settings");
        $error = false;
        $settings_list = SearchFieldsSettings::getValuesList(0, $this->cas_user->id);
        
        if(isset($settings) && !empty($settings)){
            foreach ($settings as $field_id => $setting) {
                if (isset($settings_list[$field_id])) {
                    if ($settings_list[$field_id] == $setting) {
                        continue;
                    }
                    $model_id = SearchFieldsSettings::getModelId($field_id, 0, $this->cas_user->id);
                    $model = SearchFieldsSettings::findOne($model_id);
                } else {
                    $model = new SearchFieldsSettings();
                    $model->department_id = 0;
                    $model->field_id = $field_id;
                    $model->cas_user_id = $this->cas_user->id;
                }

                $model->value = $setting;

                if(!$model->save()){
                    $error = true;
                }
            }
        } else {
            $error = true;
        }
        
        return Json::encode(['error' => $error]);
    }

}
