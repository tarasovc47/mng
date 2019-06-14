<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZonesTariffs */

$this->title = $package ? 'Создать пакетный тарифный план' : 'Создать тарифный план';
$this->params['breadcrumbs'][] = ['label' => 'Тарифные планы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-tariffs-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'extra_data' => $extra_data,
        'package' => $package,
    ]) ?>

</div>
