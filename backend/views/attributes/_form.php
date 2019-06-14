<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use common\models\ApplicationsTypes;

	$attributes = $model::loadList([], 0, 0, [], [ "department_id" => $model->department_id ]);
?>
<div class="techsup-attributes-form">
    <?php $form = ActiveForm::begin([ 'enableClientValidation' => false, 'enableAjaxValidation' => false ]); ?>
	    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
	    <?= $form->field($model, 'parent_id')->dropDownList($attributes, [ 'encode' => false ]); ?>
	    <div class="connection-technology-id">
	    	<?= $form->field($model, 'connection_technology_id')->dropDownList($model::getConnTechs(), [ 'prompt' => '' ]); ?>
	    </div>
	    <?= $form->field($model, 'application_type_id')->dropDownList(ApplicationsTypes::loadList(), [ 'prompt' => '&mdash; Наследуется  &mdash;', 'encode' => false ]); ?>
	    <?= $form->field($model, 'department_id')->textInput(['type' => 'hidden'])->label(false) ?>
	    <div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>