<?php

use yii\helpers\Html;
use common\models\ManagCompaniesBranches;

if ($branches && !$branch_id) {
	$this->title = 'Добавить контактное лицо к филиалу';
} elseif ($branches && $branch_id) {
	$branch_name = ManagCompaniesBranches::findOne($branch_id);
	$this->title = 'Добавить контактное лицо к филиалу: '.$branch_name['name'];
} else {
	$this->title = 'Добавить контактное лицо';
}

$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $company->managCompaniesTypes->short_name.' '.$company->name, 'url' => ['view', 'id' => $company_id, 'from_branch' => $branches ? true : false, 'ManagCompaniesBranchesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-contacts-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_contact_form', [
        'model' => $model,
        'model_contacts' => $model_contacts,
        'branches' => $branches,
        'branch_id' => $branch_id,
        'company_id' => $company_id,
    ]) ?>

</div>
