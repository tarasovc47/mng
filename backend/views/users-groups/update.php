<?php

use yii\helpers\Html;

$this->title = 'Группа «' . $model->name . '»';
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['/departments/index']];
$this->params['breadcrumbs'][] = ['label' => $model->department->name, 'url' => ['/departments/update', 'id' => $model->department->id]];
$this->params['breadcrumbs'][] = ['label' => 'Группа «' . $model->name . '»', 'url' => ['/users-groups/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="users-groups-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'users' => $users,
    ]) ?>
</div>