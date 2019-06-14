<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Operators */

$this->title = 'Редактировать оператора: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Операторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="operators-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
