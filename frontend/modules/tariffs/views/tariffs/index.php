<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Operators;
use common\models\Services;
use common\models\ConnectionTechnologies;

$this->title = 'Тарифные планы';
$this->params['breadcrumbs'][] = $this->title;

$servicesList = Services::loadList();
$connTechList = ConnectionTechnologies::getTechnologiesList('');
?>
<div class="zones-tariffs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::button('Показать поиск', ['class' => 'btn btn-primary tariffs__open-search']) ?>
    </p>

    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'opersList' => Operators::loadList(),
        'servicesList' => Services::loadList(),
        'connTechList' => ConnectionTechnologies::getTechnologiesList(''),
    ]); ?>

    <?php if ($this->context->permission == 2): ?>
    	<p>
	        <?= Html::a('Создать тарифный план', ['create'], ['class' => 'btn btn-success']) ?>
	        <?= Html::a('Создать пакетный тарифный план', ['create', 'package' => true], ['class' => 'btn btn-success']) ?>
	    </p>
    <?php endif ?>
   
    <?php 
    	$settings = [
	        'dataProvider' => $dataProvider,
	        'columns' => [
	            [
	               'attribute' => 'name',
	               'content' => function ($model, $key, $index, $column){
	                    return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
	                }
	            ],
	            [
	                'attribute' => 'opers',
	                'format' => 'html',
	                'content' => function ($model, $key, $index, $column){
	                    $html = '';
	                    foreach ($model->tariffsToOpers as $operator) {
	                        $html .= $operator->operators->name.'<br>';
	                    }
	                    return $html;
	                }
	            ],
	            [
	                'attribute' => 'services',
	                'format' => 'html',
	                'content' => function ($model, $key, $index, $column){
	                    $html = '';
	                    foreach ($model->tariffsToServices as $service) {
	                        $html .= $service->service->name.'<br>';
	                    }
	                    return $html;
	                }
	            ],
	            [
	                'attribute' => 'connection_technologies',
	                'format' => 'html',
	                'content' => function ($model, $key, $index, $column) {
	                    $html = '';
	                    foreach ($model->tariffsToConnTechs as $conn_tech) {
	                        $html .= $conn_tech->connTech->name.'<br>';
	                    }
	                    return $html;
	                }
	            ],
	            [
	               'attribute' => 'for_abonent_type',
	               'filter' => \Yii::$app->params['abonent_types'],
	               'content' => function ($model, $key, $index, $column){
	                    return $column->filter[$model->for_abonent_type];
	                }
	            ],
	            [
	                'attribute' => 'opened_at',
	                'content' => function ($model, $key, $index, $column){
	                    return date('d-m-Y', $model->opened_at);
	                }
	            ],
	            [
	                'attribute' => 'closed_at',
	                'content' => function ($model, $key, $index, $column){
	                    return ($model->closed_at != '') ? date('d-m-Y', $model->closed_at) : '';
	                }
	            ],
	            'price',
	            'speed',
	            'channels',
	        ],
	    ];

	    if ($this->context->permission == 2) {
	    	$settings['columns'][] = [
	                'class' => 'yii\grid\ActionColumn', 'template' => '{update}',
	                'buttons' => [
	                    'update' => function ($url, $model, $key){
	                        return Html::a('', ['update', 'id' => $model->id, 'package' => $model->package], ['title' => 'Редактировать', 'class' => 'glyphicon glyphicon-pencil']);
	                    },
	                ],
	            ];
	    }

    	echo GridView::widget($settings); 
    ?>
</div>
