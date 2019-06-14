<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Отделы компании';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departments-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Cоздать отдел', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php
            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'name',
                    'cas_name',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $template,
                    ],
                ],
            ]); 
        ?>
    <?php Pjax::end(); ?>
</div>
