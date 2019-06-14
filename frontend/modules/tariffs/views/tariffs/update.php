<?php

use yii\helpers\Html;

$this->title = 'Редактировать тарифный план: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Тарифные планы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="zones-tariffs-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'extra_data' => $extra_data,
        'package' => $package,
    ]) ?>

</div>
