<?php
use yii\widgets\ActiveForm;

use yii\widgets\ActiveField;
print_r($post);

$form = ActiveForm::begin();?>
<?= $form->field($model,'address'); ?>
<?= $form->field($model,'ip'); ?>
<?= $form->field($model,'comment'); ?>
<?= $form->field($model,'key'); ?>
<?ActiveForm::end();

