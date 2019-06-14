<?php

use yii\helpers\Html;

$this->title = 'Создание атрибута';
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['/departments/index']];
$this->params['breadcrumbs'][] = ['label' => $department->name, 'url' => ['/departments/update', 'id' => $department->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attributes-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
