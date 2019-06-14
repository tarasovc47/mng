<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Сценарии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-scenarios-view">
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

        $settings["attributes"][] = [
            "attribute" => "techsup_attribute_id",
            'value' => Html::a($model->tAttribute->name, ['/techsup/attributes/view', 'id' => $model->tAttribute->id]),
            'format' => 'html',
        ];

        $settings["attributes"][] = [
            "attribute" => "department_id",
            'value' => Html::a($model->department->name, ['/departments/view', 'id' => $model->department->id]),
            'format' => 'html',
        ];

        $settings["attributes"][] = [
            "attribute" => "status",
            'value' => $model::getStatuses($model->status),
        ];

        $settings["attributes"][] = [
            "attribute" => "descr",
            'value' => nl2br($model->descr),
            'format' => 'html',
        ];

        echo DetailView::widget($settings);
    ?>
</div>