<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Настройка доступов к модулям';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="modules-settings-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Добавить модуль', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
    <?php
    $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            'descr',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
