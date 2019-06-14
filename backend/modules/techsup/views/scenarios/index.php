<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\TechsupScenarios;
use common\models\Departments;

$this->title = 'Модуль «Техподдержка» :: Сценарии';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-scenarios-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать сценарий', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin(); ?>
        <?php
            $statuses = TechsupScenarios::getStatuses();
            $departments = Departments::loadList();
            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    [
                        'attribute' => 'techsup_attribute_id',
                        'value' => function($model){
                            return Html::a($model->tAttribute->name, ['/techsup/attributes/view', 'id' => $model->tAttribute->id], [ 'data-pjax' => 0 ]);
                        },
                        'format' => 'raw',
                    ],                    
                    [
                        'attribute' => 'department_id',
                        'filter' => $departments,
                        'value' => function($model){
                            return Html::a($model->department->name, ['/departments/view', 'id' => $model->department->id], [ 'data-pjax' => 0 ]);
                        },
                        'format' => 'raw',
                    ],
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
