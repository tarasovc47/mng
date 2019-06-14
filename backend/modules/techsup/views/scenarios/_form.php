<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Departments;
use common\widgets\AttributesTree;
?>
<div class="techsup-scenarios-form">
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'techsup_attribute_id')->textInput() ?>
        <div class="scenarios__attributes">
            <?php 
                echo AttributesTree::widget([
                    "attrs" => $attributes,
                    "template" => "backend/scenarios/form"
                ]);
            ?>
        </div>
        <?= $form->field($model, 'department_id')->dropDownList(Departments::loadList()) ?>
        <?= $form->field($model, 'status')->dropDownList($model::getStatuses()) ?>
        <?= $form->field($model, 'descr')->textarea(['rows' => 6]) ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
