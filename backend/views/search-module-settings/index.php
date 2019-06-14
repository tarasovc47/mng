<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Настройки поиска для отделов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-module-settings-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
        $template = ($this->context->permission == 2) ? '{update}' : '';

        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'descr',
                [
                    'class' => 'yii\grid\ActionColumn', 
                    'template' => $template,
                ],
            ],
        ]);
    ?>
</div>