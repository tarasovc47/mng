<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Fields;

$this->title = 'Поля атрибутов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-fields-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(); ?>
        <?php
            $statuses = Fields::getStatuses();
            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';
            
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'label',
                    'name',
                    [
                       'attribute' => 'status',
                       'filter' => $statuses,
                       'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->status];
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
