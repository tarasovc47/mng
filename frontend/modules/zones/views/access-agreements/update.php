<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ZonesAccessAgreements */

$this->title = 'Редактировать договор доступа: ' . $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Договоры доступа', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="zones-access-agreements-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
