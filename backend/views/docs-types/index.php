<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\DocsTypes;

$this->title = 'Типы документов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docs-types-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать тип документа', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php 
            $model = new DocsTypes;

            $template = ($this->context->permission == 2) ? '{update}' : '';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                       'attribute' => 'name',
                       'content' => function ($model, $key, $index, $column){
                            return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                        }
                    ],
                    [
                        'attribute' => 'sub_document',
                        'filter' => $model->sub_document_translate,
                        'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->sub_document];
                        }
                    ],
                    [
                       'attribute' => 'available_for',
                       'filter' => $model->available_for_translate,
                       'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->available_for];
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'template' => $template,
                    ],
                ],
            ]);
        ?>
    <?php Pjax::end(); ?>
</div>
