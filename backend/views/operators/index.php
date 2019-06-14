<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Операторы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="operators-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать оператора', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php
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
                    'comment:ntext',

                    [
                        'class' => 'yii\grid\ActionColumn', 
                        'template' => $template,
                    ],
                ],
            ]); 
        ?>
    <?php Pjax::end(); ?>
</div>
