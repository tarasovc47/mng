<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ManagCompany */

$this->title = 'Создать компанию';
$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
