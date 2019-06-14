<?php

use yii\helpers\Html;

$this->title = 'Создание сценария';
$this->params['breadcrumbs'][] = ['label' => 'Модуль «Техподдержка» :: Сценарии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-scenarios-create">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>
</div>
