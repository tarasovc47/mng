<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="departments-form">
    <?php $form = ActiveForm::begin(); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <?= $form->field($model, 'cas_name')->textInput(['maxlength' => true]) ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>