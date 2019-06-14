<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZonesAddressStatuses */

$this->title = 'Создать статус объекта';
$this->params['breadcrumbs'][] = ['label' => 'Статусы объектов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-address-statuses-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
