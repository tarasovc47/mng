<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="cas-user-form">
    <?php $form = ActiveForm::begin(); ?>
	    <?= $form->field($model, 'login')->textInput() ?>
	    <?= $form->field($model, 'first_name')->textInput() ?>
	    <?= $form->field($model, 'last_name')->textInput() ?>
	    <?= $form->field($model, 'middle_name')->textInput() ?>
	    <?= $form->field($model, 'group_id')
	    	->dropDownList($groups, [ 'prompt' => "&mdash; Выбрать &mdash;", 'encode' => false ])
	    	->hint("Если группа не указана, то при логине пользователя в систему автоматически выбирается первая подходящая");
	     ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
