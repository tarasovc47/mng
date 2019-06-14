<?php

use yii\helpers\Html;

$this->title = 'Создание группы';
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['/departments/index']];
$this->params['breadcrumbs'][] = ['label' => $model->department->name, 'url' => [ '/departments/update', 'id' => $model->department->id, 'tab' => 'groups' ]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-groups-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
