<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
?>
<div class="users-groups-form">
    <?php $form = ActiveForm::begin([ 'enableClientValidation' => false, 'enableAjaxValidation' => false ]); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <? if(!$model->isNewRecord): ?>
			<?= $form->field($model, 'head_id')->dropDownList($users, [ "encode" => false ]); ?>
	    <? endif ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>