<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Договоры доступа';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-access-agreements-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if ($this->context->permission == 2): ?>
		<p>
			<?= Html::a('Создать договор доступа', ['create'], ['class' => 'btn btn-success']) ?>
		</p>
    <?php endif ?>
   

    <?php 
    	$settings = [
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                   'attribute' => 'label',
                   'content' => function ($model, $key, $index, $column){
                        return Html::a($model->label, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                    }
                ],
                [
                    'attribute' => 'oper_id',
                    'filter' => $opers,
                    'content' => function ($model, $key, $index, $column){
                        return $column->filter[$model->oper_id];
                    }
                ],
                [
                    'attribute' => 'manag_company_id',
                    'filter' => $companies,
                    'content' => function ($model, $key, $index, $column){
                        return $column->filter[$model->manag_company_id];
                    }
                ],
                [
                    'attribute' => 'opened_at',
                    'filterInputOptions' => [
                        'class' => 'form-control zones__agreements-index__opened-at',
                        'readonly' => true,
                    ],
                    'content' => function ($model, $key, $index, $column){
                        return date('d-m-Y', $model->opened_at);
                    }
                ],
                [
                    'attribute' => 'auto_prolongation',
                    'filter' => [
                        0 => 'Нет', 
                        1 => 'Да'
                    ],
                    'content' => function ($model, $key, $index, $column){
                        return ($model->auto_prolongation) ? 'Да' : 'Нет';
                    }
                ],
                [
                    'attribute' => 'closed_at',
                    'filterInputOptions' => [
                        'class' => 'form-control zones__agreements-index__closed-at',
                        'readonly' => true,
                    ],
                    'content' => function ($model, $key, $index, $column){
                        return ($model->closed_at != 0) ? date('d-m-Y', $model->closed_at) : '';
                    }
                ],
                'comment:ntext',
                'rent_price',
            ],
        ];

        if ($this->context->permission == 2) {
        	$settings['columns'][] = ['class' => 'yii\grid\ActionColumn', 'template' => '{update}'];
        }

        echo GridView::widget($settings); 
    ?>

