<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Attributes;
use common\models\ApplicationsStatuses;
use yii\helpers\ArrayHelper;

$this->title = "Поле «" . $model->label . "»";
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['breadcrumbs'][] = ['label' => $target->name, 'url' => [ $url . '/view', 'id' => $target->id ]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-fields-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?php
        $settings = [
            'model' => $model,
            'attributes' => [
                'label',
                'name',
            ],
        ];

        $settings['attributes'][] = [
            "attribute" => "descr",
            'value' => nl2br($model->descr),
            "format" => "html",
        ];
        
        $settings["attributes"][] = [
            "attribute" => "target_id",
            'value' => Html::a($target->name, [$url . '/view', 'id' => $target->id]),
            'format' => 'html',
        ];

        $data = unserialize($model->data);
        $data_value = '';
        foreach($data as $param => $value){
            $data_value .= "<strong>" . $model->getAttributeLabel($param) . ":</strong> ";

            switch($param){
                case "required":
                    $data_value .= ($value == 1) ? "Да" : "Нет";
                    break;
                case "type":
                    $data_value .= $model->typeList[$value];
                    break;
                case "allowedValues": 
                    foreach($value as $name){
                        $data_value .= "<br>" . $name;
                    }
                    break;
                case "view": 
                    $data_value .= $model->viewList[$value];
                    break;
                case "cardinality":
                    $data_value .= $model->cardinalityList[$value];
                    break;
                case "format": 
                    $data_value .= $model->formatList[$value];
                    break;
                default:
                    $data_value .= $value;
            }

            $data_value .= "<br>";
        }

        $settings["attributes"][] = [
            "attribute" => "data",
            'value' => $data_value,
            'format' => 'html',
        ];

        echo DetailView::widget($settings); 
    ?>
</div>