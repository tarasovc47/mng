<?php
$this->params['breadcrumbs'][] = ['label' => 'Инструменты'];
$this->params['breadcrumbs'][] = ['label' => 'Конфигурации терминалов '];
use yii\grid\GridView;

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,

    'tableOptions' => [
        'class' => 'table table-striped table-hover'
    ],
    'rowOptions' => function ($model, $key, $index, $grid) {
        $server = trim(explode("=",explode("\n",$model->config)[0])[1]);
        switch ($server){
            case "192.168.80.251":
                $color = '#CCFF66';
                break;
            case "192.168.80.252":
                $color = '#CCCC66';
                break;
            case "192.168.80.253":
                $color = '#CCCCCC';
                break;
            case "192.168.80.254":
                $color = '#CCFFFF';
                break;
            default:
                $color ='#FFFFCC';
                break;
        }

        return ['style' => [
            'background'=>$color
        ]];
    },
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'ip',
        'mac',
        'user',
        'config:ntext',
        // 'created_at',
        // 'updated_at',

        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}&nbsp;&nbsp;{delete}'],
    ],
]);