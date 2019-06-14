<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\AddressSearch;

/* @var $this yii\web\View */
/* @var $model common\models\ManagCompaniesBranches */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="manag-companies-branches-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput() ?>
    
    <?= AddressSearch::widget([
        'model' => $model,
        'attribute' => "actual_address_id",
        'template' => 'place-editable',
        'place' => $model->actual_address_id,
    ]); ?>

    <?= $form->field($model, 'coordinates')->textInput(['placeholder' => 'Выберите точку на карте и это поле заполнится автоматически']) ?>

    <div id="manag-companies__map"></div>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
