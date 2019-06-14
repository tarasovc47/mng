<?php

use yii\helpers\Html;

$this->title = 'Создание статуса';
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Статусы заявок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-statuses-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>