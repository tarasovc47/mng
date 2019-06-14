<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Новый модуль';
$this->params['breadcrumbs'][] = ['label' => 'Настройка доступов к модулям', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departments-create container">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-4"><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-sm-8"><?= $form->field($model, 'descr')->textInput(['maxlength' => true]) ?></div>
    </div>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
