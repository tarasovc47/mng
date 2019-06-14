<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\SiteHelper;
use common\models\ZonesDistrictsAndAreas;
use common\models\ZonesAddressTypes;
use common\models\ManagCompanies;
use common\models\ZonesAccessAgreements;
use common\models\ZonesAddressStatuses;
use common\models\UsersGroups;
use common\models\ManagCompaniesBranches;
use common\models\ManagCompaniesToContacts;

$this->title = SiteHelper::getAddressNameByUuid($model->address_uuid);
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="zones__address__tab active"><a href="#zones__address__information" data-toggle="tab">Общая информация</a></li>
        <li class="zones__address__tab"><a href="#zones__address__individual" data-toggle="tab">Физические лица</a></li>
        <li class="zones__address__tab"><a href="#zones__address__entity" data-toggle="tab">Юридические лица</a></li>
        <li class="zones__address__tab"><a href="#zones__address__sсheme" data-toggle="tab">Схема объекта</a></li>
        <li class="zones__address__tab"><a href="#zones__address__map" data-toggle="tab">Посмотреть на карте</a></li>
    </ul>

    <div class="zones__address__tab-content tab-content">
        <div class='tab-pane active' data-tab='zones__address__information' id="zones__address__information">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Адрес',
                        'format' => 'html',
                        'value' => SiteHelper::getAddressNameByUuid($model->address_uuid),
                        
                    ],
                    [
                        'label' => 'Округ',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $district = ZonesDistrictsAndAreas::findOne($model->district_id)['name'];
                            if (!empty($district)) {
                                return Html::a($district, ['/zones/districts-and-areas/view', 'id' => $model->district_id]);
                            } else {
                                return '';
                            }                            
                        },
                        
                    ],
                    [
                        'label' => 'Район',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $area = ZonesDistrictsAndAreas::findOne($model->area_id)['name'];
                            if (!empty($area)) {
                                return Html::a($area, ['/zones/districts-and-areas/view', 'id' => $model->area_id]);
                            } else {
                                return '';
                            }                            
                        },
                        
                    ],
                    [
                        'label' => 'Группа службы эксплуатации',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $users_group_id = ZonesDistrictsAndAreas::findOne($model->area_id)['users_group_id'];
                            $users_group = UsersGroups::findOne($users_group_id)['name'];
                            if (!empty($users_group)) {
                                return $users_group;
                            } else {
                                return '';
                            }                            
                        },
                    ],
                    [
                        'label' => 'Тип',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $type = ZonesAddressTypes::findOne($model->address_type_id)['name'];
                            if (!empty($type)) {
                                return $type;
                            } else {
                                return '';
                            }                            
                        },
                        
                    ],
                    [
                        'attribute' => 'manag_company_id',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $company = ManagCompanies::findOne($model->manag_company_id);
                            if (!empty($company)) {
                                return Html::a($company->managCompaniesTypes['short_name'].' '.$company['name'], ['/manag-companies/view', 'id' => $model->manag_company_id, 'ManagCompaniesBranchesSearch[company_id]' => $model->manag_company_id, 'ManagCompaniesBranchesSearch[publication_status]' => 1]);
                            } else {
                                return '';
                            }                            
                        },
                        
                    ],
                    [
                        'label' => 'Участок',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $branch = ManagCompaniesBranches::findOne($model->manag_company_branch_id)['name'];
                            if (!empty($branch)) {
                                return $branch;
                            }

                            return;                           
                        },
                        
                    ],
                    [
                        'label' => 'У кого брать ключи',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            if ($model->key_keeper != '') {
                                $contact = ManagCompaniesToContacts::getOneContact($model->manag_company_branch_id, $model->key_keeper);
                                $html = '';
                                if (!empty($contact)) {
                                    $html .= '<strong>'.$contact['name'].'</strong><br>'
                                                        .$contact['office'].'<br>'
                                                        .$contact['phones'].'<br>'
                                                        .$contact['emails'].'<br>'
                                                        .$contact['comments'];
                                    return $html;
                                }
                            }
                            return;    
                                                
                        },
                        
                    ],
                    [
                        'label' => 'Заключён договор с управлющей компанией',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            switch ($model->contract_with_manag_company) {
                                case '0':
                                    return 'Нет';
                                    break;
                                case '1':
                                    return 'Да';
                                    break;
                                
                                default:
                                    return '';
                                    break;
                            }
                        }
                    ],
                    [
                        'label' => 'Договоры доступа',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $html = '';
                            foreach ($model->addressesToAgreements as $key_agreement => $agreement) {
                                $html .= $agreement->agreement->label.' ('.date('d-m-Y', $agreement->agreement->opened_at).' - ';
                                if ($agreement->agreement->auto_prolongation) {
                                    $html .= 'с автопролонгацией';
                                } else {
                                    $html .= date('d-m-Y', $agreement->agreement->closed_at);
                                }
                                $html .= ')<br>';
                            }
                            return $html;
                        },
                        
                    ],
                    [
                        'label' => 'Операторы',
                        'format' => 'html',
                        'value' => function ($model, $widget) use ($extra_data){
                            $html = '';
                            foreach ($extra_data['opers_list'] as $key_oper => $oper) {
                                $html .= $oper['name'].'<br>';
                            }
                            return $html;
                        },
                        
                    ],
                    [
                        'label' => 'Координаты',
                        'contentOptions' => [
                            'class' => 'hidden zones-addresses__view__coordinates',
                            'data' => [
                                'coordinates' => $model->coordinates,
                            ],
                        ],
                        'captionOptions' => [
                            'class' => 'hidden',
                        ],
                        'value' => $model->coordinates,
                        
                    ],
                    'comment:ntext',
                ],
            ]) ?>
        </div>

        <div class='tab-pane' data-tab='zones__address__individual' id="zones__address__individual">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Статус объекта',
                        'format' => 'html',
                        'value' => ZonesAddressStatuses::findOne($model->build_status_individual)['name'],
                        
                    ],
                    'connection_cost_individual',
                    [
                        'label' => 'Сервисы и технологии подключения',
                        'format' => 'html',
                        'value' => function ($model, $widget) use ($extra_data){
                            $html = '';
                            $html .= '<ul class="zones-addresses__view__techs-individual">';
                            foreach ($extra_data['services_and_techs_list_individual'] as $service) {
                                $html .= '<li>'.$service['name'];
                                if (!empty($service['conn_techs'])) {
                                    $html .= ':<ul>';
                                    foreach ($service['conn_techs'] as $tech) {
                                        $html .= '<li>'.$tech.'</li>';
                                    }
                                    $html .= '</ul>';
                                }
                                
                                $html .= '</li>';
                            }
                            $html .= '</ul>';
                            return $html;
                        },
                    ],
                ],
            ]) ?>

            <?php 
                if (!empty($extra_data['tariffs_list_individual'])){
                    echo $this->render('_tariffs_view', [
                        'tariffs_list' => $extra_data['tariffs_list_individual'],
                    ]); 
                }
            ?>
            
        </div> 

        <div class='tab-pane' data-tab='zones__address__entity' id="zones__address__entity">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Статус объекта',
                        'format' => 'html',
                        'value' => ZonesAddressStatuses::findOne($model->build_status_entity)['name'],
                        
                    ],
                    'connection_cost_entity',
                    [
                        'label' => 'Сервисы и технологии подключения',
                        'format' => 'html',
                        'value' => function ($model, $widget) use ($extra_data){
                            $html = '';
                            $html .= '<ul class="zones-addresses__view__techs-entity">';
                            foreach ($extra_data['services_and_techs_list_entity'] as $service) {
                                $html .= '<li>'.$service['name'];
                                if (!empty($service['conn_techs'])) {
                                    $html .= ':<ul>';
                                    foreach ($service['conn_techs'] as $tech) {
                                        $html .= '<li>'.$tech.'</li>';
                                    }
                                    $html .= '</ul>';
                                }
                                
                                $html .= '</li>';
                            }
                            $html .= '</ul>';
                            return $html;
                        },
                    ],
                ],
            ]) ?>

            <?php 
                if (!empty($extra_data['tariffs_list_entity'])){
                    echo $this->render('_tariffs_view', [
                        'tariffs_list' => $extra_data['tariffs_list_entity'],
                    ]); 
                }
            ?>
        </div>

        <div class='tab-pane' data-tab='zones__address__sсheme' id="zones__address__sсheme">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'all_flats',
                    'all_offices',
                ],
            ]) ?>
            <div class="zones__address-view__scheme">
                <ul class="nav nav-tabs">
                    <?php $i = 1; ?>
                    <?php foreach ($object_scheme as $key_porch => $porch): ?> 
                         <li class="zones__address__tab-porch <?php if ($i == 1): ?> active <?php endif ?>">
                            <a href="#porch-<?=$key_porch ?>" data-toggle="tab">Подъезд <?=$porch['porch_name']?></a>
                        </li>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="zones__address__tab-content tab-content">

                <?php $i = 1; ?>
                <?php foreach ($object_scheme as $key_porch => $porch): ?>
                    <div class='tab-pane <?php if ($i == 1): ?> active <?php endif ?>' data-tab='porch-<?=$key_porch ?>' id="porch-<?=$key_porch ?>">
                        <?php foreach ($porch['floors_item'] as $key_floor => $floor): ?>
                            <div class="floor-row">
                                <div class="floor">
                                    <strong><?=$floor ?> эт.</strong>
                                 </div>
                                <?php if (isset($porch['offices_item'][$key_floor])): ?>
                                    <?php foreach ($porch['offices_item'][$key_floor] as $key_office => $office): ?>
                                        <div class="office bg-warning">
                                            <?=$office ?> оф.
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>

                                <?php if (isset($porch['flats_item'][$key_floor])): ?>
                                    <?php foreach ($porch['flats_item'][$key_floor] as $key_flat => $flat): ?>
                                        <div class="flat bg-success">
                                            <?=$flat ?> кв.
                                        </div>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <?php $i++; ?>
                <?php endforeach; ?>
                </div>
            </div>
        </div> 

        <div class='tab-pane' data-tab='zones__address__map' id="zones__address__map">
            <div id="zones__address-view__map-place" data-coordinates="<?=$model->coordinates ?>"></div>
        </div>
    </div>
</div>
