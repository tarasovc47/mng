<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ManagCompaniesTypes */

$this->title = 'Создать тип';
$this->params['breadcrumbs'][] = ['label' => 'Типы управляющих компаний', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
