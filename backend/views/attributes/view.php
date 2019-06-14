<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\data\ActiveDataProvider;
use common\models\Attributes;
use common\models\Fields;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Отделы компании', 'url' => ['/departments/index']];
$this->params['breadcrumbs'][] = ['label' => $model->department->name, 'url' => ['/departments/update', 'id' => $model->department->id]];
$this->params['breadcrumbs'][] = 'Атрибут «' . $this->title . '»';
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php
    $details = $this->render('_details', [
        'model' => $model,
    ]);

    $fields = false;
    if(count($model->fields) > 0){
        $fieldsDataProvider = new ActiveDataProvider([
            'query' => Fields::find()->where(["target_id" => $model->id, "target_table" => Attributes::tableName()]),
        ]);
        $fields = $this->render('@backend/views/fields/_tab-content', [
            'dataProvider' => $fieldsDataProvider,
            'target' => $model,
            'table' => Attributes::tableName(),
        ]);
    }

    $settings = [
        'items' => [
            [
                'label' => 'Атрибут',
                'content' => $details,
                'active' => true,
            ],
        ],
    ];

    if($fields){
        $fields = [
            'label' => 'Поля',
            'content' => $fields
        ];
        $settings['items'][] = $fields;
    }

    echo Tabs::widget($settings);
?>