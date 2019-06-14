<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="tariffs-groups-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'abonent_type')->dropDownList(Yii::$app->params['abonent_types'], ['prompt' => '']) ?>

    <?= $form->field($model, 'tariffs')->dropDownList($tariffs_list, ['multiple' => 'multiple', 'disabled' => (empty($tariffs_list)) ? true : false]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
