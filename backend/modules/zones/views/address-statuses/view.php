<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ZonesAddressStatuses */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Статусы объектов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-address-statuses-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?php 

    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => 'Необходима привязка тарифов в зонах присутствия',
                'value' => $model->tariffsRequiredStatuses[$model->tariffs_required],
            ],
            'comment:ntext',
        ],
    ]) ?>

</div>
