<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Services;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Технологии подключения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="connection-technologies-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?php
        $settings = [
            'model' => $model,
            'attributes' => [
                'name',
            ],
        ];

        $settings['attributes'][] = [
            "attribute" => "comment",
            'value' => nl2br($model->comment),
            "format" => "html",
        ];

        $service = Services::findOne($model->service_id);
        $settings["attributes"][] = [
            "attribute" => "service_id",
            'value' => Html::a($service->name, ['/services/view', 'id' => $service->id]),
            'format' => 'html',
        ];

        echo DetailView::widget($settings); 
    ?>
</div>
