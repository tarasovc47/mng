<?php

use yii\helpers\Html;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['/departments/index']];
$this->params['breadcrumbs'][] = ['label' => $department->name, 'url' => ['/departments/update', 'id' => $department->id]];
$this->params['breadcrumbs'][] = ['label' => 'Атрибут «' . $model->name . '»', 'url' => ['/attributes/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="attributes-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
	        'model' => $model,
	    ]);
    ?>
</div>