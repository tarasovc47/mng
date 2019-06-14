<?php

use yii\helpers\Html;

$this->title = 'Редактировать контактное лицо: '.$model->contactFaces->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $company->managCompaniesTypes->short_name.' '.$company->name, 'url' => ['view', 'id' => $model->company_id, 'ManagCompaniesBranchesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = 'Редактировать контактное лицо';
?>
<div class="manag-companies-contacts-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_contact_form', [
        'model' => $model,
        'branches' => $branches,
        'company_id' => $model->company_id,
    ]) ?>

</div>
