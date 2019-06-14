<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DocsTypes */

$this->title = 'Создание типа документа';
$this->params['breadcrumbs'][] = ['label' => 'Типы документов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docs-types-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
