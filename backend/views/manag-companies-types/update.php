<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ManagCompaniesTypes */

$this->title = 'Редактировать тип ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Типы управляющих компаний', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="manag-companies-types-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
