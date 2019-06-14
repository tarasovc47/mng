<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ZonesDistrictsAndAreas;
use common\models\UsersGroups;

$this->title = 'Округа и районы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="districts-and-areas-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if ($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать округ или район', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
    <?php endif ?>

    <?php Pjax::begin(); ?>    
        <?php 
            $parents = ZonesDistrictsAndAreas::getDistrictList();
            $parents[-1] = '—';

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
                       'attribute' => 'type',
                       'filter' => $types,
                       'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->type];
                        }
                    ],
                    [
                       'attribute' => 'parent_id',
                       'filter' => $parents,
                       'content' => function ($model, $key, $index, $column){
                            return $column->filter[$model->parent_id];
                        }
                    ],
                    [
                       'attribute' => 'users_group_id',
                       'filter' => ArrayHelper::map(UsersGroups::find()->where(['department_id'=>2])->all(), 'id', 'name'),
                       'content' => function ($model, $key, $index, $column){
                            return UsersGroups::findOne($model->users_group_id)['name'];
                        }
                    ],
                    'comment:ntext',
                ],
            ];

            if ($this->context->permission == 2){
                $settings['columns'][] = ['class' => 'yii\grid\ActionColumn', 'template' => "{update}"];
            }

            echo GridView::widget($settings); 
        ?>
    <?php Pjax::end(); ?>
</div>
