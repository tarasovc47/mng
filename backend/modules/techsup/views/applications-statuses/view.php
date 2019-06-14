<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Статусы заявок', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1><?= Html::encode($this->title) ?></h1>
<?php
    $form = $this->render('_details', [
        'model' => $model,
    ]);

    echo Tabs::widget([
        'items' => [
            [
                'label' => 'Статус',
                'content' => $form,
                'active' => true,
            ],
            [
                'label' => 'Поля',
                'content' => $fields,
            ],
        ],
    ]);
?>