<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Operators;
use common\models\ManagCompanies;


$opersList = Operators::getOpersList();
$companiesList = ManagCompanies::getCompaniesList();

if ($model->opened_at != '') {
    $model->opened_at = date('d-m-Y', $model->opened_at);
}
if ($model->closed_at != '') {
    $model->closed_at = date('d-m-Y', $model->closed_at);
}
?>

<div class="zones-access-agreements-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false, 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?php echo ($model->name == null) ? $form->field($model, 'file')->fileInput() : ''; ?>

    <?= $form->field($model, 'label')->textInput() ?>

    <?= $form->field($model, 'oper_id')->dropDownList($opersList) ?>

    <?= $form->field($model, 'manag_company_id')->dropDownList($companiesList) ?>

    <?= $form->field($model, 'opened_at')->textInput([ "readonly" => true ]) ?>

    <?= $form->field($model, 'auto_prolongation')->checkbox() ?>

    <div class="zones__agreements__closed_at <?php if ($model->auto_prolongation) { echo 'hidden'; } ?>">
        <?= $form->field($model, 'closed_at')->textInput([ "readonly" => true ]) ?>
    </div>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'rent_price')->textInput() ?>

    <?= $form->field($model, 'price_is_ratio')->checkbox() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
