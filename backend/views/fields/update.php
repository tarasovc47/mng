<?php

use yii\helpers\Html;
use common\models\Attributes;

$this->title = "Поле «" . $model->label . "»";
$this->params['breadcrumbs'] = $breadcrumbs;
$this->params['breadcrumbs'][] = ['label' => $target->name, 'url' => [$url . '/view', 'id' => $target->id]];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="techsup-fields-update">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
