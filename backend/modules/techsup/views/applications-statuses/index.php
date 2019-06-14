<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Модуль «Техподдержка» :: Статусы заявок';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-statuses-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать статус', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin(); ?>
        <?php
            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $template,
                    ],
                ],
            ]); 
        ?>
    <?php Pjax::end(); ?>
</div>
