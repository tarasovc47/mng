<?php

use yii\helpers\Html;

$this->title = 'Создание типа';
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Типы заявок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-types-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
