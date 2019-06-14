<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactFaces */

$this->title = 'Редактировать контактное лицо: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Контактные лица', 'url' => ['index', 'ContactFacesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="contact-faces-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>