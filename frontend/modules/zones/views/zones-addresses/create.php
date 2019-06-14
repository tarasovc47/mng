<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Zones */

$this->title = 'Создать адрес';
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'extra_data' => $extra_data,
        'mass_create' => false,
        'tariffs' => $tariffs,
    ]) ?>

</div>
