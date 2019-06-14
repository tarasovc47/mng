<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\DocsTypes;
use common\models\Services;
use common\models\ConnectionTechnologies;

?>

<div class="docs-archive-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false, 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= Html::button('Загрузить новую версию документа', ['id' => 'docsarchive-load_new_file', 'class' => $model->isNewRecord ? 'hidden' : 'btn btn-info'])?>

    <?= $form->field($model, 'file')->fileInput(['class' => $model->isNewRecord ? '' : 'hidden'])->label('Загрузить документ', ['class' => $model->isNewRecord ? 'control-label' : 'hidden']) ?>

    <?= $form->field($model, 'client_id')->dropDownList($client_ids) ?>

    <?= $form->field($model, 'loki_basic_service_ids')->dropDownList($user_ids, ['multiple' => 'multiple']) ?>

    <?= $form->field($model, 'service_types')->dropDownList(Services::loadList(), ['multiple' => 'multiple']) ?>

    <?= $form->field($model, 'conn_techs')->dropDownList(($model->service_types) ? ConnectionTechnologies::getTechnologiesList($model->service_types) : [], ['multiple' => 'multiple', 'disabled' => ($model->service_types) ? false : 'disabled']) ?>

    <?= $form->field($model, 'doc_type_id')->dropDownList(DocsTypes::getDocTypesList($sub_doc), ['prompt' => '']) ?>

    <?= $form->field($model, 'billing_contract_id')->dropDownList($contracts, ['prompt' => '']) ?>

    <?= $form->field($model, 'billing_contract_name')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'billing_contract_date')->hiddenInput()->label(false); ?>
    <?= $form->field($model, 'billing_contract_type')->hiddenInput()->label(false); ?>

    <?= $form->field($model, 'label')->textInput() ?>

    <?= $form->field($model, 'opened_at')->textInput(['value' => $model->opened_at ? date('dd-mm-YY', $model->opened_at) : '']) ?>

    <?= $form->field($model, 'descr')->textArea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
