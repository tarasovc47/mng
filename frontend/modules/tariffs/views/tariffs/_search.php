<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Zones;

/* @var $this yii\web\View */
/* @var $model common\models\search\ZonesTariffsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zones-tariffs-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
        'enableClientValidation' => false, 
        'enableAjaxValidation' => false,
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'priority')->dropDownList($model->priority_tariff, ['prompt' => '']) ?>

    <?= $form->field($model, 'public')->dropDownList($model->public_tariff, ['prompt' => '']) ?>

    <?= $form->field($model, 'package')->dropDownList($model->package_tariff, ['prompt' => '']) ?>

    <?= $form->field($model, 'opers')->dropDownList($opersList, ['multiple' => 'multiple']) ?>

    <?= $form->field($model, 'services')->dropDownList($servicesList, ['multiple' => 'multiple']) ?>

    <?= $form->field($model, 'connection_technologies')->dropDownList($connTechList, ['multiple' => 'multiple']) ?>    

    <?= $form->field($model, 'for_abonent_type')->dropDownList(\Yii::$app->params['abonent_types'], ['prompt' => '']) ?>

    <?= $form->field($model, 'opened_at')->textInput() ?>

    <?= $form->field($model, 'closed_at')->textInput() ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'speed')->textInput() ?>
    
    <?= $form->field($model, 'channels')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Искать', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-default tariffs__reset-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
