<?php

use yii\helpers\Html;

$this->title = 'Создание поля';
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['breadcrumbs'][] = ['label' => $target->name, 'url' => [$url . '/view', 'id' => $target->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-fields-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
