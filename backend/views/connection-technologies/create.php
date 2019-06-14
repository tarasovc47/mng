<?php

use yii\helpers\Html;

$this->title = 'Создание технологии подключения';
$this->params['breadcrumbs'][] = ['label' => 'Технологии подключения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="connection-technologies-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
