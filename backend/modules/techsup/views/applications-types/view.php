<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Типы заявок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-types-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
        ],
    ]) ?>
</div>
