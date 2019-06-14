<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$isNewRecord = $model->isNewRecord;
?>
<div class="techsup-fields-form">
    <?php $form = ActiveForm::begin([ 'enableClientValidation' => false, 'enableAjaxValidation' => false ]); ?>
	    <?= $form->field($model, 'label')->textInput(['maxlength' => true]) ?>

	    <? if($isNewRecord): ?>
	    	<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		<? endif ?>

	    <?= $form->field($model, 'descr')->textarea(['rows' => 6]) ?>
	    <?= $form->field($model, 'status')->dropDownList($model::getStatuses()) ?>
	    <div class="create-field-block form-group">

			<? if($isNewRecord): ?>
				<?= $form->field($model, 'type')->dropDownList($model->typeList, ["prompt" => ""]) ?>
			<? endif ?>

			<div class="general-field-params">
				<?= $form->field($model, 'required')->checkbox() ?>
			    <?= $form->field($model, 'default_value')->textInput() ?>
	    		<?= $form->field($model, 'cardinality')->dropDownList($model->cardinalityList)->hint("Максимальное количество значений, которые пользователи могут ввести для этого поля.") ?>
		   	</div>

		   	<div class="private-field-params">
		   		<div class="field-section number <? if($model->type == 'number'){ echo " show"; } ?>">
	   				<?= $form->field($model, 'min')->textInput(['type' => 'number']) ?>
	   				<?= $form->field($model, 'max')->textInput(['type' => 'number']) ?>
		   		</div>
		   		<div class="field-section list <? if($model->type == 'list'){ echo " show"; } ?>">
		   			<?= $form->field($model, 'allowedValues')
		   					->textarea(['rows' => 6])
		   					->hint("Значения, которые может содержать это поле. Введите по одному значению на каждой строке в виде: ключ|подпись.<br>
							Ключ - это сохраняемое значение, он должен быть <strong>числовым</strong>. Подпись используется в выводимых значениях.");
					?>
	    			<?= $form->field($model, 'view')->dropDownList($model->viewList) ?>
		   		</div>
		   		<div class="field-section datetime <? if($model->type == 'datetime'){ echo " show"; } ?>">
	    			<?= $form->field($model, 'format')->dropDownList($model->formatList) ?>
		   		</div>
		   	</div>
	    </div>
	    <div class="form-group">
	        <?= Html::submitButton($isNewRecord ? 'Создать' : 'Сохранить', ['class' => $isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
    <?php ActiveForm::end(); ?>
</div>
