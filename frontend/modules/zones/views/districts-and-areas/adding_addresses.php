<?php
	use yii\helpers\Html;
	use frontend\widgets\MultipleAddressesForm;
	use yii\widgets\ActiveForm;

	$this->title = 'Привязать ещё адреса к району '.$modelArea->name;
	$this->params['breadcrumbs'][] = ['label' => 'Округа и районы', 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => $modelArea->name, 'url' => ['view', 'id' => $modelArea->id]];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="districtandareas-adding-addresses">

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

    <?= $form->field($dynModel, 'area_id')->hiddenInput(['value' => $modelArea->id])->label(false) ?>

    <?= $form->field($dynModel, 'district_id')->hiddenInput(['value' => $modelArea->parent_id])->label(false) ?>

    <?= MultipleAddressesForm::widget([
            'model' => $modelArea,
            'attribute' => '',
            'template' => 'place-find-editable',
        ]); 
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>