<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="departments-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
	    $form = $this->render('_form', [
	        'model' => $model,
	    ]);

	    $access = $this->render('_access', [
	    	'accessSettings' => $accessSettings,
	    ]);

	    $search_settings = $this->render('_search_settings', [
            'department_fields_values' => $department_fields_values,
            'fields' => $fields,
            'department_id' => $model->id,
	    ]);

	    $attributes = $this->render('_attributes', [
	    	"department" => $model,
	    	"attributes" => $attributes,
	    ]);

	    $groups = $this->render('_groups', [
	    	"department" => $model,
	    	"groups" => $groups,
	    	"undefined_users" => $undefined_users,
	    ]);

	    $items = [
	        [
	            'label' => 'Основные данные',
	            'content' => $form,
	            'active' => !Yii::$app->request->get("tab"),
	        ],
	        [
	            'label' => 'Доступы',
	            'content' => $access,
	        ],
	        [
	            'label' => 'Настройки поиска',
	            'content' => $search_settings,
	        ],
	        [
	            'label' => 'Группы отдела',
	            'content' => $groups,
	            'active' => (Yii::$app->request->get("tab") == "groups"),
	        ],
	        [
	            'label' => 'Атрибуты',
	            'content' => $attributes,
	            'active' => (Yii::$app->request->get("tab") == "attributes"),
	        ],
	    ];

	    if($model->id === 1){
	    	$properties = $this->render('_properties', [
		    	"department" => $model,
		    	"properties" => $properties,
		    ]);

		    $items[] = [
		    	'label' => 'Атрибуты закрытия',
	            'content' => $properties,
	            'active' => (Yii::$app->request->get("tab") == "properties"),
		    ];
	    }

	    echo Tabs::widget([
		    'items' => $items,
		]);
	?>
</div>
