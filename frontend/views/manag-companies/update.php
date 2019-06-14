<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ManagCompany */

$this->title = 'Редактировать компанию: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->managCompaniesTypes->short_name.' '.$model->name, 'url' => ['view', 'id' => $model->id, 'ManagCompaniesBranchesSearch[publication_status]' => 1, 'ManagCompaniesBranchesSearch[company_id]' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="manag-companies-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
