<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Группы тарифных планов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariffs-groups-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if ($this->context->permission == 2): ?>
    	<p>
	        <?= Html::a('Создать группу', ['create'], ['class' => 'btn btn-success']) ?>
	    </p>
    <?php endif ?>
    
    <?php
    	$settings = [
	        'dataProvider' => $dataProvider,
	        'filterModel' => $searchModel,
	        'columns' => [
	        	[
	               'attribute' => 'name',
	               'content' => function ($model, $key, $index, $column){
	                    return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
	                }
	            ],
	            [
	               'attribute' => 'abonent_type',
	               'filter' => Yii::$app->params['abonent_types'],
	               'content' => function ($model, $key, $index, $column){
	                    return Yii::$app->params['abonent_types'][$model->abonent_type];
	                }
	            ],
	            [
	                'attribute' => 'tariffs',
	                'format' => 'html',
	                'content' => function ($model, $key, $index, $column){
	                    $html = '';
	                    foreach ($model->tariffsToGroups as $key => $value) {
	                        $html .= Html::a($value->tariff->name.'<br>', '/tariffs/tariffs/view?id='.$value->tariff->id);
	                    }
	                    return $html;
	                }
	            ],
	            'comment',
	        ],
	    ];

	    if ($this->context->permission == 2) {
	    	$settings['columns'][] = ['class' => 'yii\grid\ActionColumn', 'template' => '{update}'];
	    }

    	echo GridView::widget($settings); ?>
</div>
