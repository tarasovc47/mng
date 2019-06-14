<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->last_name . " " . $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>
<div class="cas-user-view">
    <h1><?= Html::encode($this->title) ?></h1>
    <?
    $model->roles=\common\components\SiteHelper::to_postgre_array($model->roles);
    if($this->context->permission == 2): ?>
        <p><?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
    <? endif ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'cas_id',
            'login',
            'roles',
            'first_name',
            'last_name',
            'middle_name',
        ],
    ]);
    ?>
</div>
