<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\TechsupScenariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="techsup-scenarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'techsup_attribute_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'department_id') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'descr') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
