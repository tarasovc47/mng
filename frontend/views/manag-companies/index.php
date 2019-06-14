<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ManagCompaniesTypes;

$this->title = 'Компании, предоставляющие доступ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать компанию', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
 
    <p>
        <?= Html::checkbox('show_only_publicated', $publication_status ? true : false, ['label' => 'Показывать только активные', 'class' => 'manag-companies__show-only-publicated']) ?>
    </p>
<?php
$script = <<< JS
$(document).ready(function() {
$('.manag-companies__show-only-publicated').click(function(){
var e = $.Event("keydown", {keyCode: 13});
var val = $('.manag-companies__show-only-publicated').prop('checked') ? 1 : '';
$('.manag-companies__publication-status').val(val).trigger(e);
});
});
JS;
$this->registerJs($script);
?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'publication_status',
                'filterInputOptions' => [
                    'class' => 'manag-companies__publication-status hidden'
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
                    return Html::a($model->managCompaniesTypes->short_name.' '.$model->name, ['view', 'id' => $model->id, 'ManagCompaniesBranchesSearch[company_id]' => $model->id, 'ManagCompaniesBranchesSearch[publication_status]' => 1], ['title' => 'Просмотр']);
                },
            ],
            [
                'attribute' => 'company_type',
                'filter' => ManagCompaniesTypes::getTypesList(),
                'content' => function ($model, $key, $index, $column){
                    return $model->managCompaniesTypes->name;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{update}{remove-recover}',
                'buttons' => [
                    'remove-recover' => function ($url, $model, $key){
                        if ($model->publication_status) {
                            return Html::a('', ['remove', 'id' => $model->id], 
                            [
                                'title' => 'Удалить', 
                                'class' => 'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => 'Уверены что хотите удалить данную компанию?',
                                    'method' => 'post',
                                ],
                            ]);
                        } else {
                            return Html::a('', ['recover', 'id' => $model->id], 
                            [
                                'title' => 'Восстановить', 
                                'class' => 'glyphicon glyphicon-plus',
                                'data' => [
                                    'confirm' => 'Уверены что хотите восстановить данную компанию?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                        
                        
                    },
                ],

            ],
        ],
    ]); ?>
</div>
