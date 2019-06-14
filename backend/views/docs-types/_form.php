<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="docs-types-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?php 
    	if ($model->isNewRecord) {
    		echo $form->field($model, 'folder')->textInput();
    	}   
    ?>

    <?= $form->field($model, 'sub_document')->checkbox() ?>

    <?= $form->field($model, 'available_for')->dropDownList($model->available_for_translate) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
