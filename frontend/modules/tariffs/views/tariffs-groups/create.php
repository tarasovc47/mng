<?php

use yii\helpers\Html;

$this->title = 'Создать группу';
$this->params['breadcrumbs'][] = ['label' => 'Группы тарифных планов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariffs-groups-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'tariffs_list' => $tariffs_list,
    ]) ?>

</div>
