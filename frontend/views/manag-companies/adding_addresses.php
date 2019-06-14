<?php
	use yii\helpers\Html;
	use frontend\widgets\MultipleAddressesForm;
	use yii\widgets\ActiveForm;

	$this->title = 'Привязать адреса к '.$modelCompany->managCompaniesTypes->short_name.' '.$modelCompany->name;
	$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $modelCompany->managCompaniesTypes->short_name.' '.$modelCompany->name, 'url' => ['view', 'id' => $modelCompany->id, 'ManagCompaniesBranchesSearch[company_id]' => $modelCompany->id, 'ManagCompaniesBranchesSearch[publication_status]' => 1]];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="manag-companies-adding-addresses">

    <?php if (!empty($dynModel->getErrors())): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($dynModel->getErrors() as $key => $errors): ?>
                    <?php foreach ($errors as $key => $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false]); ?>

    <?= $form->field($dynModel, 'company_id')->hiddenInput(['value' => $modelCompany->id])->label(false) ?>

    <?= $form->field($dynModel, 'branch_id')->dropDownList($branches_list, ['prompt' => ''])->label('Участок') ?>

    <?= $form->field($dynModel, 'key_keeper')->dropDownList($key_keeper_list, ['prompt' => ''])->label('У кого брать ключи') ?>

    <?= MultipleAddressesForm::widget([
            'model' => $modelCompany,
            'attribute' => '',
            'template' => 'place-find-editable',
        ]); 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>