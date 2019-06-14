<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Departments;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cas-user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php
            $departments = Departments::loadList();

            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'login',
                    'first_name',
                    'last_name',
                    'middle_name',
                    [
                       'attribute' => 'department_id',
                       'filter' => $departments,
                       'content' => function ($model, $key, $index, $column){
                            return isset($column->filter[$model->department_id]) ?
                                $column->filter[$model->department_id] :
                                "<span class='not-set'>Не задан</span>";
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
