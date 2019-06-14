<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Типы документов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docs-types-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                [
                    'label' => 'Является подчинённым документом',
                    'format' => 'html',
                    'value' => $model->sub_document_translate[$model->sub_document],
                ],
                [
                    "attribute" => "available_for",
                    'value' => $model->available_for_translate[$model->available_for],
                ],
            ],
        ]);
    ?>
</div>