<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use common\models\ApplicationsTypes;

	$properties = $model::loadList();
?>
<div class="techsup-attributes-form">
    <?php $form = ActiveForm::begin([ 'enableClientValidation' => false, 'enableAjaxValidation' => false ]); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
	    <?= $form->field($model, 'parent_id')->dropDownList($properties, [ 'encode' => false ]); ?>
	    <?= $form->field($model, 'application_type_id')->dropDownList(ApplicationsTypes::loadList(), [ 'prompt' => '&mdash; Наследуется  &mdash;', 'encode' => false ]); ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>