<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ZonesAddressStatusesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Статусы объектов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-address-statuses-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать статус объекта', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
               'attribute' => 'name',
               'content' => function ($model, $key, $index, $column){
                    return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                }
            ],
            'comment:ntext',

            ['class' => 'yii\grid\ActionColumn', 'template' => "{update}"],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
