<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Группы тарифных планов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tariffs-groups-view">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?php if ($this->context->permission == 2): ?>
    	<p>
	        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
	    </p>
    <?php endif ?>
    

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'abonent_type',
                'format' => 'html',
                'value' => function($model, $widget){
                    return Yii::$app->params['abonent_types'][$model->abonent_type];
                }
            ],
            [
                'attribute' => 'tariffs',
                'format' => 'html',
                'value' => function($model, $widget){
                    $html = '';
                    foreach ($model->tariffsToGroups as $key => $value) {
                        $html .= Html::a($value->tariff->name.'<br>', '/tariffs/tariffs/view?id='.$value->tariff->id);
                    }
                    return $html;
                }
            ],
            'comment',
        ],
    ]) ?>

</div>