<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ManagCompaniesTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Типы управляющих компаний';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-types-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать тип', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
               'attribute' => 'name',
               'content' => function ($model, $key, $index, $column){
                    return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                }
            ],
            'short_name',
            'comment:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}'
            ],
        ],
    ]); ?>
</div>
