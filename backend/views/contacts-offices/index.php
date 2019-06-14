<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Должности контактных лиц';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contacts-offices-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать должность', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'publication_status',
                'filterInputOptions' => [
                    'class' => 'contacts-offices__publication-status hidden'
                ],
                'contentOptions' => [
                    'class' => 'hidden'
                ],
                'headerOptions' => [
                    'class' => 'hidden'
                ],
                'filterOptions' => [
                    'class' => 'hidden'
                ],

            ],
            [
                'attribute' => 'name',
                'content' => function ($model, $key, $index, $column){
                        return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                    },
            ],
            [
                'attribute' => 'publication_status',
                'filter' => ['0' => 'Удалено', '1' => 'Опубликовано'],
                'content' => function ($model, $key, $index, $column){
                    return $column->filter[$model->publication_status];
                },
            ],
            'comment:ntext',

            [
                'class' => 'yii\grid\ActionColumn', 'template' => '{update}',
                'buttons' => [
                    'remove' => function ($url, $model, $key){
                        return Html::a('', ['remove', 'id' => $model->id], 
                            [
                                'title' => 'Удалить', 
                                'class' => 'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => 'Уверены что хотите удалить данную должность?',
                                    'method' => 'post',
                                ],
                            ]);
                    }
                ],

            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
