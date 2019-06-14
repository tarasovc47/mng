<?php

use yii\helpers\Html;
use yii\grid\GridView;

$template = ($this->context->permission == 2) ? '{update}' : '';

$this->title = 'Настройки поиска для отделов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="departments-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',

            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => $template,
            ],
        ],
    ]); ?>
</div>
