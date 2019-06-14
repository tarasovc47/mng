<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ConnectionTechnologies;
use common\models\Services;

$this->title = 'Технологии подключения';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="connection-technologies-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <? if($this->context->permission == 2): ?>
        <p><?= Html::a('Создать технологию подключения', ['create'], ['class' => 'btn btn-success']) ?></p>
    <? endif ?>
    <?php Pjax::begin([ 'enablePushState' => false ]); ?>
        <?php
            $services = Services::loadListWithGlobalServices();
            $servicesFilter = [];

            foreach($services as $global_service => $srvs){
                foreach($srvs as $id => $service){
                    $servicesFilter[$id] = $service; 
                }
            }

            $template = ($this->context->permission == 2) ? '{view} {update}' : '{view}';

            echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        'name',
                        [
                            'attribute' => 'service_id',
                            'filter' => Html::activeDropDownList($searchModel, 'service_id', $services, ['class' => 'form-control', 'prompt' => '']),
                            'value' => function($model) use ($servicesFilter){
                                return $servicesFilter[$model->service_id];
                            }
                        ],
                        'billing_id',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $template,
                        ],
                    ],
            ]); 
        ?>
    <?php Pjax::end(); ?>
</div>
