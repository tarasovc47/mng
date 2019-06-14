<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsOffices */

$this->title = 'Редактировать должность: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Должности контактных лиц', 'url' => ['index', 'ContactsOfficesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="contacts-offices-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
