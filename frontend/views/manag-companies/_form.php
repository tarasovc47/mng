<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\widgets\AddressSearch;
use common\models\ManagCompaniesTypes;

$companiesList = $model::getCompaniesList();
unset($companiesList[$model->id]);
$empty[-1] = 'Отсутствует';
$companiesList = ArrayHelper::merge($empty, $companiesList);
?>

<div class="manag-companies-form">

    <?php $form = ActiveForm::begin([ 'enableClientValidation' => false, 'enableAjaxValidation' => false ]); ?>

    <?= $form->field($model, 'name')->textInput()->hint('Внимание! Не нужно в названии указывать аббревиатуры УК, ТСЖ и т.д. Они назначаются автоматически при выборе типа компании.') ?>

    <?= $form->field($model, 'company_type')->dropDownList(ManagCompaniesTypes::getTypesList()) ?>

    <?= $form->field($model, 'parent_id')->dropDownList($companiesList) ?>

    <?= $form->field($model, 'abonent')->textInput() ?>

    <?= AddressSearch::widget([
        'model' => $model,
        'attribute' => "jur_address_id",
        'template' => 'place-editable',
        'place' => $model->jur_address_id,
    ]); ?>

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
