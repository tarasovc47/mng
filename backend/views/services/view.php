<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\GlobalServices;
use common\models\Services;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Сервисы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="services-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?php
    	$settings = [
            'model' => $model,
            'attributes' => [
                'name',
	            'machine_name',
	            'billing_id',
            ],
        ];

        if($model->global_service_id){
            $globalService = GlobalServices::findOne($model->global_service_id);
            $settings['attributes'][] = [
                "attribute" => "global_service_id",
                'value' => "<a href='/global-services/view?id=" . $globalService->id . "'>" . $globalService->name . "</a>",
                'format' => 'html',
            ];
        }            

        $settings['attributes'][] = [
            "attribute" => "status",
            'value' => Services::getStatuses($model->status),
        ];

	    echo DetailView::widget($settings);
	?>
</div>
