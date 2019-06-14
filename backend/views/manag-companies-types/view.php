<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ManagCompaniesTypes */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Типы управляющих компаний', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-types-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'short_name',
            'comment:ntext',
        ],
    ]) ?>

</div>
