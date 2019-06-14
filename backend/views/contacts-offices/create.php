<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ContactsOffices */

$this->title = 'Создать должность';
$this->params['breadcrumbs'][] = ['label' => 'Должности контактных лиц', 'url' => ['index', 'ContactsOfficesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contacts-offices-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
