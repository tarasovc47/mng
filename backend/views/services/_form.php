<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Services;
use common\models\GlobalServices;
?>
<div class="services-form">
    <?php $form = ActiveForm::begin(); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <?= $form->field($model, 'machine_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'global_service_id')->dropDownList(GlobalServices::loadList()); ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
