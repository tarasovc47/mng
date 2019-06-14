<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\SiteHelper;
use common\models\ManagCompaniesToContacts;
use common\models\ManagCompaniesTypes;
use common\models\ZonesAccessAgreements;
use common\models\ManagCompaniesBranches;


$this->title = $model->managCompaniesTypes->short_name.' '.$model->name;
$this->params['breadcrumbs'][] = ['label' => 'Компании, предоставляющие доступ', 'url' => ['index', 'ManagCompaniesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="manag-companies-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li class="manag_companies__tab  <?php if (!$branches): ?>active<?php endif ?>"><a href="#information" data-toggle="tab">Общая информация</a></li>
        <li class="manag_companies__tab <?php if ($branches): ?>active<?php endif ?>"><a href="#branches" data-toggle="tab">Участки</a></li>
        <li class="manag_companies__tab"><a href="#addresses" data-toggle="tab">Обслуживаемые адреса</a></li>
        <li class="manag_companies__tab"><a href="#manag-companies__general-map" data-toggle="tab">Посмотреть на карте</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="manag_companies__tab-content tab-content">
        <div class='tab-pane <?php if (!$branches): ?>active<?php endif ?>' data-tab='information' id="information" data-company-coord="<?php echo $model->coordinates ?>">
            <p>
                <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Добавить контактное лицо', ['create-contact', 'company_id' => $model->id, 'branches' => false], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Снять с публикации', ['remove', 'id' => $model->id], ['class' => 'btn btn-danger']) ?>
            </p>

            <?php

            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'label' => 'Название',
                        'format' => 'html',
                        'value' => $model->name,
                    ],
                    [
                        'label' => 'Тип компании',
                        'format' => 'html',
                        'value' => ManagCompaniesTypes::findOne($model->company_type)['name'],
                        
                    ],
                    [
                        'label' => 'Юридический адрес',
                        'format' => 'html',
                        'value' => SiteHelper::getAddressNameByUuid($model->jur_address_id, $cas_login),
                        
                    ],
                    [
                        'label' => 'Фактический адрес',
                        'format' => 'html',
                        'value' => SiteHelper::getAddressNameByUuid($model->actual_address_id, $cas_login),
                        
                    ],
                    [
                        'label' => 'Родительская компания',
                        'format' => 'html',
                        'value' => Html::a($model::findOne($model->parent_id)['name'], ['view', 'id' => $model->parent_id]) ,
                        
                    ],
                    [
                        'label' => 'Номер абонента',
                        'format' => 'html',
                        'value' => Html::a($model->abonent, ['/abonent/index', 'abonent' => $model->abonent]) ,
                        
                    ],
                    'comment:ntext',
                    [
                        'label' => 'Контактные лица',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $contacts = ManagCompaniesToContacts::getCompanyContactsForView($model->id);
                            $contacts_html = '';
                            $contacts_html .= '<ul class="manag_companies__contact_ul>';
                            foreach ($contacts as $contact) {
                                $contacts_html .= '<li class="manag_companies__contact_li"><div class="manag_companies__contact">';
                                $contacts_html .= '<strong>'.Html::a($contact['name'], ['/contact-faces/view', 'id' => $contact['contact_id']]).'</strong><br>'
                                                .$contact['office'].'<br>'
                                                .$contact['phones'];

                                if ($contact['emails'] != '') {
                                    $contacts_html .= '<br>'.$contact['emails'];
                                }
                                if ($contact['comments'] != '') {
                                    $contacts_html .= '<br><div class="manag-companies__contact-comments">'.$contact['comments'].'</div>';
                                }

                                $contacts_html .= '<div class="manag_companies__contact_actions">'.
                                                    Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-contact', 'id' => $contact['id']], ['title' => 'Редактировать', 'class' => 'manag_companies__contact_update']).
                                                    Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-contact', 'id' => $contact['id']], ['title' => 'Удалить', 'class' => 'manag_companies__contact_delete']).
                                                    
                                                    '</div>';
                                $contacts_html .= '</div></li>';
                            }
                            $contacts_html .= '</ul>';     
                            return $contacts_html;                
                        },
                    ],
                    [
                        'label' => 'Договоры доступа',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $agreements = ZonesAccessAgreements::getAgreementsForCompany($model->id);
                            $html = '<ul class="manag_companies__agreements_ul>';
                            foreach ($agreements as $key => $agreement) {
                                $html .= '<li><strong>'
                                            .Html::a($agreement['label'], ['/zones/access-agreements/view', 'id' => $agreement['id']])
                                            .'</strong><br>c '
                                            .date('d-m-Y', $agreement['opened_at']);

                                if ($agreement['auto_prolongation']) {
                                    $html .= ' (с автопролонгацией)';
                                } else {
                                    $html .= ' по '.date('d-m-Y', $agreement['closed_at']);
                                }

                                $html .= '</li>';
                            }
                            $html .= '</ul>';
                            return $html;
                        },
                    ]
                ],
            ]) ?>
        </div>

        <div class='tab-pane <?php if ($branches): ?>active<?php endif ?>' data-tab='branches' id="branches">
            <p>
                <?= Html::a('Создать участок', ['create-branch', 'company_id' => $model->id], ['class' => 'btn btn-success']) ?>
                <?= Html::a('Добавить контактное лицо', ['create-contact', 'company_id' => $model->id, 'branches' => true], ['class' => 'btn btn-primary']) ?>
            </p>
            <?php Pjax::begin(); ?>    
                <?php
                echo GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'attribute' => 'publication_status',
                            'filterInputOptions' => [
                                'class' => 'manag-companies__branches__publication-status hidden',
                            ],
                            'contentOptions' => [
                                'class' => 'hidden'
                            ],
                            'headerOptions' => [
                                'class' => 'hidden'
                            ],
                            'filterOptions' => [
                                'class' => 'hidden',
                            ],

                        ],
                        [
                           'attribute' => 'company_id',
                           'visible' => false,
                        ],
                        [
                            'attribute' => 'name',
                            'contentOptions' => function ($model_branch, $key, $index, $column){
                                return ['data' => ['branch-coordinates' => $model_branch->coordinates, 'branch-name' => $model_branch->name]];
                            },
                        ],
                        [
                            'attribute' => 'actual_address_id',
                            'format' => 'html',
                            'content' => function ($model_branch, $key, $index, $column) use ($cas_login){
                                return SiteHelper::getAddressNameByUuid($model_branch->actual_address_id, $cas_login);
                            },
                            
                        ],
                        [
                            'attribute' => 'contacts',
                            'format' => 'html',
                            'content' => function ($model_branch, $key, $index, $column) use ($model){
                                $contacts = ManagCompaniesToContacts::getCompanyContactsForView($model->id, $model_branch->id);
                                $contacts_html = '';
                                if (!empty($contacts)) {
                                    $contacts_html .= '<ul class="manag_companies__contact_ul">';
                                    foreach ($contacts as $contact) {
                                        $contacts_html .= '<li class="manag_companies__contact_li"><div class="manag_companies__contact">';
                                        $contacts_html .= '<strong>'.$contact['name'].'</strong><br>'
                                                            .$contact['office'].'<br>'
                                                            .$contact['phones'];

                                        if ($contact['emails'] != '') {
                                            $contacts_html .= '<br>'.$contact['emails'];
                                        }
                                        if ($contact['comments'] != '') {
                                            $contacts_html .= '<br><div class="manag-companies__contact-comments">'.$contact['comments'].'</div>';
                                        }

                                        $contacts_html .= '<div class="manag_companies__contact_actions">'.
                                                            Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-contact', 'id' => $contact['id'], 'branches' => true], ['title' => 'Редактировать', 'class' => 'manag_companies__contact_update']).
                                                            Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-contact', 'id' => $contact['id'], 'branches' => true], ['title' => 'Удалить', 'class' => 'manag_companies__branch_contact_delete']).
                                                            '</div>';
                                        $contacts_html .= '</div></li>';
                                    }
                                    $contacts_html .= '</ul>';
                                }
                                return $contacts_html;
                            }
                        ],
                        [
                           'attribute' => 'comment',
                           'format' => 'ntext',
                           'filterOptions' => [
                                'class' => 'hidden',
                            ],
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn', 
                            'template' => '{update} {create_contact} {remove}',
                            'buttons' => [
                                'update' => function ($url, $model_branch, $key) use ($model){
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update-branch', 'company_id' => $model->id, 'branches' => true, 'id' => $model_branch->id], ['title' => 'Редактировать']);
                                },
                                'create_contact' => function ($url, $model_branch, $key) use ($model){
                                    return Html::a('<i class="fa fa-user-plus fa-lg" aria-hidden="true"></i>', ['create-contact', 'company_id' => $model->id, 'branches' => true, 'branch_id' => $model_branch->id], ['title' => 'Добавить контактное лицо']);
                                },
                                'remove' => function ($url, $model_branch, $key) use ($model){
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['remove-branch', 'company_id' => $model->id, 'branches' => true, 'id' => $model_branch->id], ['title' => 'Снять с публикации']);
                                },
                            ],
                        ],
                    ],
                ]); ?>
            <?php Pjax::end(); ?>
        </div>

        <div class='tab-pane' data-tab='addresses' id="addresses">
            <p><?= Html::a('Привязать адреса', ['adding-addresses', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
            <table class="table table-striped table-bordered">
                <tbody>
                <?php foreach ($addresses as $key_branch => $branch): ?>
                    <tr>
                        <td><strong>
                            <?php
                                if ($key_branch != '') {
                                    echo ManagCompaniesBranches::findOne($key_branch)['name'];
                                } else {
                                    echo 'Без привязки к участку';
                                }
                             
                            ?>
                        </td></strong>
                    </tr>

                    <?php foreach ($branch as $key_address => $address): ?>
                        <tr>
                            <td>
                                <?= Html::a(SiteHelper::getAddressNameByUuid($address), ['/zones/zones-addresses/view', 'id' => $key_address], ['class' => 'manag-companies__view__address_link']); ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                <?php endforeach ?>
                </tbody>
            </table> 
        </div>

        <div class='tab-pane' data-tab='manag-companies__general-map' id="manag-companies__general-map"></div>
    </div>
</div>