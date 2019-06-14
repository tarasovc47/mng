<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ConnectionTechnologies;
use common\models\Services;
use common\models\Fields;
use common\models\ApplicationsTypes;
?>
<div class="techsup-attributes-view">
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?php 
        $settings = [
            'model' => $model,
            'attributes' => [],
        ];

        if($model->parent_id != 0){
            $parent = $model::findOne($model->parent_id);
            $settings['attributes'][] = [
                "attribute" => "parent_id",
                'value' => "<a href='/attributes/view?id=".$model->parent_id."'>" . $parent->name . "</a>",
                'format' => 'html',
            ];
        }

        if($model->connection_technology_id != 0){
            $connTech = ConnectionTechnologies::findOne($model->connection_technology_id);
            $service = Services::findOne($connTech->service_id);

            $settings["attributes"][] = [
                "label" => $connTech->getAttributeLabel("service_id"),
                'value' => Html::a($service->name, ['/services/view', 'id' => $service->id]),
                'format' => 'html',
            ];

            $settings["attributes"][] = [
                "attribute" => "connection_technology_id",
                'value' => Html::a($connTech->name, ['/connection-technologies/view', 'id' => $connTech->id]),
                'format' => 'html',
            ];
        }

        $settings['attributes'][] = [
            "attribute" => "comment",
            'value' => nl2br($model->comment),
            "format" => "html",
        ];

        if($model->application_type_id != 0){
            $types = ApplicationsTypes::loadList();
            $settings['attributes'][] = [
                "attribute" => "application_type_id",
                'value' => Html::a($types[$model->application_type_id], ['/techsup/applications-types/view', 'id' => $model->application_type_id]),
                "format" => "html",
            ];
        }

        echo DetailView::widget($settings);
    ?>
</div>