<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ZonesAddressStatuses */

$this->title = 'Редактировать статус объекта: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Статусы объектов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="zones-address-statuses-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
