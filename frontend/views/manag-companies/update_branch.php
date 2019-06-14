<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ManagCompaniesBranches */

$this->title = 'Редактировать участок: '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $company->managCompaniesTypes->short_name.' '.$company->name, 'url' => ['view', 'id' => $company_id, 'from_branch' => true, 'ManagCompaniesBranchesSearch[publication_status]' => 1, 'ManagCompaniesBranchesSearch[company_id]' => $company_id]];
$this->params['breadcrumbs'][] = 'Редактировать участок';
?>
<div class="manag-companies-branches-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_branch_form', [
        'model' => $model,
    ]) ?>

</div>
