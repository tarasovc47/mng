<?php

use yii\helpers\Html;

$this->title = 'Редактировать группу: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы тарифных планов', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="tariffs-groups-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tariffs_list' => $tariffs_list,
    ]) ?>

</div>
