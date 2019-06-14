<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Services;
use common\models\GlobalServices;


$this->title = 'Сервисы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="services-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать сервис', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php
            $statuses = Services::getStatuses();
            $globalServices = GlobalServices::loadList();

            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    'machine_name',
                    [
                       'attribute' => 'global_service_id',
                       'filter' => $globalServices,
                       'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->global_service_id];
                        }
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
