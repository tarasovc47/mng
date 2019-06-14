<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Services;

$services = Services::loadListWithGlobalServices();
?>
<div class="connection-technologies-form">
    <?php $form = ActiveForm::begin(); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
	    <?= $form->field($model, 'service_id')->dropDownList($services) ?>
	    <?= $form->field($model, 'billing_id')->textInput(['type' => 'number']) ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
