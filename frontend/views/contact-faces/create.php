<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ContactFaces */

$this->title = 'Создать контактное лицо';
$this->params['breadcrumbs'][] = ['label' => 'Контактные лица', 'url' => ['index', 'ContactFacesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-faces-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
