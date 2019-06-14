<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Fields;
?>
<div class="techsup-fields-tab-content">
	<p><?= Html::a('Создать поле', ['fields/create', 'id' => $target->id, 'target' => $table], ['class' => 'btn btn-success']) ?></p>
    <?php
    	$statuses = Fields::getStatuses();
    	$template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';
    	
	    echo GridView::widget([
	        'dataProvider' => $dataProvider,
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
	            	'buttons' => [
	            		'update' => function($url, $model, $key){
					        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', "/fields/update?id=" . $model->id, ['title' => 'Редактировать']);
					    },
					    'view' => function($url, $model, $key){
					        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', "/fields/view?id=" . $model->id, ['title' => 'Просмотр']);
					    },
	            	],
	            ],
	        ],
	    ]); 
	?>
</div>