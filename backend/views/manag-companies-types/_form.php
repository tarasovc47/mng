<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="manag-companies-types-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => true]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?= $form->field($model, 'short_name')->textInput() ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
