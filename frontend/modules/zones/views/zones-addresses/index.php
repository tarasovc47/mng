<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\SiteHelper;
use common\models\Operators;
use common\models\ZonesDistrictsAndAreas;
use common\models\ManagCompanies;
use common\models\ZonesAddressTypes;

$this->title = 'Адреса';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::button('Показать поиск', ['class' => 'btn btn-primary zones__addresses-view__open-search']) ?>
    </p>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if ($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать адрес', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Создать группу адресов', ['mass-create'], ['class' => 'btn btn-warning']) ?>
        </p>
    <?php endif ?>
    
    <?php  
        $settings = [
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                   'attribute' => 'address_uuid',
                   'content' => function ($model, $key, $index, $column){
                        return Html::a(SiteHelper::getAddressNameByUuid($model->address_uuid), ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                    }
                ],
                [
                   'attribute' => 'address_type_id',
                   'content' => function ($model, $key, $index, $column){
                        return ZonesAddressTypes::findOne($model->address_type_id)['name'];
                    }
                ],
                [
                   'attribute' => 'district_id',
                   'content' => function ($model, $key, $index, $column){
                        return ZonesDistrictsAndAreas::findOne($model->district_id)['name'];
                    }
                ],
                [
                   'attribute' => 'area_id',
                   'content' => function ($model, $key, $index, $column){
                        return ZonesDistrictsAndAreas::findOne($model->area_id)['name'];;
                    }
                ],
                [
                   'attribute' => 'manag_company_id',
                   'content' => function ($model, $key, $index, $column){
                        $company = ManagCompanies::findOne($model->manag_company_id);
                        if (!empty($company)) {
                            return $company->managCompaniesTypes['short_name'].' '.$company['name'];
                        } else {
                            return '';
                        }
                    }
                ],
                [
                   'attribute' => 'opers',
                   'content' => function ($model, $key, $index, $column){
                        $html = '';
                        foreach ($model->addressesToOpers as $key => $oper) {
                            $html .= $oper->operator->name.'<br>';
                        }
                        return $html;
                    }
                ],
                'comment:ntext',
            ],
        ];

        if ($this->context->permission == 2) {
            $settings['columns'][] = ['class' => 'yii\grid\ActionColumn', 'template' => "{update}"];
        }
        
        echo GridView::widget($settings); 
    ?>
</div>