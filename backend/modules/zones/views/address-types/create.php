<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZonesAddressTypes */

$this->title = 'Создание типа адреса';
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Зоны присутствия» :: Типы адресов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-address-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
