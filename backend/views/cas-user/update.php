<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = $model->last_name . " " . $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = "Редактирование";
?>
<div class="cas-user-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php
	    $form = $this->render('_form', [
	        'model' => $model,
	        'groups' => $groups,
	    ]);

	    $access = $this->render('_access', [
	    	'accessSettings' => $accessSettings,
	    ]);

	    echo Tabs::widget([
		    'items' => [
		        [
		            'label' => 'Основные данные',
		            'content' => $form,
		            'active' => true,
		        ],
		        [
		            'label' => 'Доступы',
		            'content' => $access,
		        ],
		    ],
		]);
	?>
</div>
