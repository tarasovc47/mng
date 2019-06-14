<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Operators;
use common\models\Services;
use common\models\ConnectionTechnologies;
use common\components\SiteHelper;

$services_options = $package ? ['prompt' => '', 'multiple' => 'multiple', 'data' => ['package' => 1]] : ['prompt' => '', 'data' => ['package' => 0]];
$billing_id_options = $package ? ['prompt' => '', 'multiple' => 'multiple', 'data' => ['package' => 1], 'disabled' => (empty($model->services)) ? 'disabled' : false] : ['prompt' => '', 'data' => ['package' => 0], 'disabled' => (empty($model->services)) ? 'disabled' : false];
?>

<div class="zones-tariffs-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false]); ?>

    <?= $form->field($model, 'services')->dropDownList($extra_data['services_list'], $services_options) ?>

    <?= $form->field($model, 'connection_technologies')->dropDownList($extra_data['conn_tech_list'], ['multiple' => 'multiple', 'disabled' => (empty($model->services)) ? 'disabled' : false ])->hint('Выберите технологии подключения (при этом должны быть выбраны сервисы).') ?>

    <?= $form->field($model, 'billing_id')->dropDownList($extra_data['tariffs_list'], $billing_id_options)->hint('Выберите тарифный план из биллинга (при этом должны быть выбраны сервисы).') ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'public')->checkbox() ?>
    
    <?= $form->field($model, 'priority')->checkbox() ?>

    <?= $form->field($model, 'for_abonent_type')->dropDownList($extra_data['abon_types']) ?>

    <?= $form->field($model, 'opers')->dropDownList($extra_data['opers_list'], ['multiple' => 'multiple']) ?>

    <?= $form->field($model, 'opened_at')->textInput(['value' => ($model->opened_at != '') ? (date("d-m-Y", $model->opened_at)) : '']) ?>

    <?= $form->field($model, 'closed_at')->textInput(['value' => ($model->closed_at != '') ? (date("d-m-Y", $model->closed_at)) : '']) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <?= $form->field($model, 'speed')->textInput() ?>

    <?= $form->field($model, 'channels')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
