<?php

use yii\helpers\Html;

$this->title = 'Создание отдела';
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departments-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
