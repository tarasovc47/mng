<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ZonesAccessAgreements */

$this->title = 'Создать договор доступа';
$this->params['breadcrumbs'][] = ['label' => 'Договоры доступа', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-access-agreements-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
