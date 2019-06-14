<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<div class="applications-statuses-view">
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