<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\models\DocsArchive;
use common\models\DocsTypes;

$this->title = $model->label;
if ($model->abonent) {
   $this->params['breadcrumbs'][] = ['label' => 'Карточка абонента '.$model->abonent, 'url' => '/abonent/abonent/index?abonent='.$model->abonent];
   $this->params['breadcrumbs'][] = ['label' => 'Документы абонента '.$model->abonent, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[abonent]='.$model->abonent.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
} elseif ($model->client_id) {
    $this->params['breadcrumbs'][] = ['label' => 'Карточка лицевого счёта '.$model->client_id, 'url' => '/abonent/abonent/index?client_id='.$model->client_id];
    $this->params['breadcrumbs'][] = ['label' => 'Документы по лицевому счёту '.$model->client_id, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[client_id]='.$model->client_id.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
}
if ($model->parent_id != -1) {
    $parent_sub_doc = (DocsArchive::findOne($model->parent_id)['parent_id'] == -1) ? 0 : 1;
    $this->params['breadcrumbs'][] = ['label' => DocsArchive::findOne($model->parent_id)['label'], 'url' => '/abonent/docs-archive/view?id='.$model->parent_id.'&DocsArchiveSearch[parent_id]='.$model->parent_id.'&sub_doc='.$parent_sub_doc.'&DocsArchiveSearch[publication_status]=1'];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docs-archive-view">

    <h1><?= Html::a($model->label, $model->name, ['target' => '_blank']) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id, 'sub_doc' => $sub_doc], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Скачать', [$model->name], ['class' => 'btn btn-primary', 'download' => true]) ?>
        <?= Html::a(($model->publication_status) ? 'Удалить' : 'Восстановить', 
                    null, 
                    [
                        'class' => 'btn btn-danger', 
                        'id' => 'view__remove-button', 
                        'data' => [
                            'doc-id' => $model->id, 
                            'current-status' => $model->publication_status
                        ]
                    ]) 
        ?>
    </p>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs docs-archives__nav-tabs">
        <li class="docs-archives__tab active"><a href="#docs-archives__parent" data-toggle="tab"><?= $model->label ?></a></li>
        <li class="docs-archives"><a href="#docs-archives__children" data-toggle="tab">Подчинённые документы</a></li>
    </ul>

    <div class="docs-archives__tab-content tab-content">
        <div class='tab-pane active' data-tab='docs-archives__parent' id="docs-archives__parent">
            <?php

            $settings = [
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'doc_type_id',
                        'value' => DocsTypes::findOne($model->doc_type_id)['name'],
                    ],
                    'label',
                    [
                        'attribute' => 'opened_at',
                        'value' => date('d.m.Y', $model->opened_at),
                    ],
                ],
            ];

            if ($model->parent_id != -1) {
                $settings['attributes'][] = [
                    'attribute' => 'parent_id',
                    'format' => 'html',
                    'value' => Html::a($model::findOne($model->parent_id)['label'], ['view', 'id' => $model->parent_id, 'sub_doc' => $parent_sub_doc, 'DocsArchiveSearch[parent_id]' => $model->parent_id]),
                ];
            }

            if (!empty($model->abonent)) {
                $settings['attributes'][] = 'abonent';
            }
                    
            $settings['attributes'][] = 'client_id';
            $settings['attributes'][] = 'descr';

            $settings['attributes'][] = [
                        'attribute' => 'loki_basic_service_ids',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $html = '';
                            foreach ($model->lokiBasicServiceIds as $key => $lbs) {
                                $html .= $model::getOneLokiBasicServiceId($lbs['loki_basic_service_id']);
                                $html .= '<br>';
                            }
                            return $html;
                        },
                    ];

            $settings['attributes'][] = [
                        'label' => 'Типы сервисов и технологии подключения',
                        'format' => 'html',
                        'value' => function ($model, $widget){
                            $html = '<ul class="service_types_list">';
                            foreach ($model->serviceTypes as $key => $service) {
                                $html .= '<li><strong>'.$service->name.'</strong><ul>';
                                foreach ($model->connTechs as $key => $tech) {
                                    if ($tech->service_id == $service->id) {
                                        $html .= '<li>'.$tech->name.'</li>';
                                    }
                                }
                                $html .= '</ul></li>';
                            }

                            $html .= '</ul>';
                            return $html;
                        },
                    ];

            echo DetailView::widget($settings);

            ?>

            <div class="docs-archive__document-view">
                <?php if ($model->extension != null && $model->extension == 'pdf'): ?>
                    <h3>Просмотр документа</h3>
                    <iframe src="<?php echo $model->name ?>"></iframe>
                <?php endif ?>
            </div>
        </div>

        <div class='tab-pane' data-tab='docs-archives__children' id="docs-archives__children">
            <div class="form-group">
                <?= Html::a('Создать подчинённый документ', ['/abonent/docs-archive/create', 'sub_doc' => 1, 'parent_id' => $model->id], ['class' => 'btn btn-info']); ?>
            </div>

<?php Pjax::begin() ?>
<?php
$script = <<< JS
$(document).ready(function() {
$('.docs-archive__cas-id select').chosen({
'allow_single_deselect': true,  
'no_results_text': 'Нет совпадений',
'placeholder_text_single': ' ',
'width' : '100%',
});
});
JS;
$this->registerJs($script);
?>
                <?php
                    $user_ids = array();
                    if ($model->abonent) {
                       $client_ids = DocsArchive::getClientIDs($model->abonent);
                       foreach ($client_ids as $key => $client) {
                           $user_ids[$client] = DocsArchive::getLokiBasicServiceIDsList($client);
                        }
                        $users = [];
                        foreach($user_ids as $key => $usrs){
                            foreach($usrs as $id => $user){
                                $users[$id] = $user;
                            }
                        }
                    } elseif ($model->client_id) {
                        $user_ids = DocsArchive::getLokiBasicServiceIDsList($model->client_id);
                        $users = $user_ids;
                    }

                    $settings = [
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'label',
                                'format' => 'html',
                                'content' => function ($model, $key, $index, $column){
                                    return Html::a($model->label, [
                                            'view', 
                                            'id' => $model->id,
                                            'sub_doc' => 1,
                                            'DocsArchiveSearch[parent_id]' => $model->id,
                                        ], 
                                        [
                                            'data' => [
                                                'pjax' => 0,
                                            ],
                                        ]
                                    );
                                }

                            ],
                            [
                                'attribute' => 'parent_id',
                                'filterInputOptions' => [
                                    'class' => 'hidden'
                                ],
                                'contentOptions' => [
                                    'class' => 'hidden'
                                ],
                                'headerOptions' => [
                                    'class' => 'hidden'
                                ],
                                'filterOptions' => [
                                    'class' => 'hidden'
                                ],

                            ],
                            [
                               'attribute' => 'doc_type_id',
                               'filter' => DocsTypes::getDocTypesList(1),
                               'content' => function ($model, $key, $index, $column){
                                    return $column->filter[$model->doc_type_id];
                                }
                            ],
                            [
                               'attribute' => 'abonent',
                               'visible' => false
                            ],
                        ],
                    ];

                                                
                    $settings['columns'][] = [
                                               'attribute' => 'descr',
                                            ];

                    $settings['columns'][] = [
                                               'attribute' => 'opened_at',
                                               'format' => ['date', 'php:d-m-Y'],
                                               'filterOptions' => ['class' => 'docs-archive__opened_at'],
                                            ];

                    $settings['columns'][] = [
                                    'attribute' => 'publication_status',
                                    'filter' => [
                                        '1' => 'Опубликован',
                                        '0' => 'Удалён',
                                    ],
                                    'content' => function ($model, $key, $index, $column){
                                        return $column->filter[$model->publication_status];
                                    }
                                ];

                    $settings['columns'][] = [
                                                'class' => 'yii\grid\ActionColumn',
                                                'template' => "{update} {add}",
                                                'buttons' => [
                                                    'add' => function ($url,$model) {
                                                        return Html::a(
                                                        '<i class="fa fa-paperclip " aria-hidden="true" title = "Создать подчинённый документ"></i>', 
                                                        ['/abonent/docs-archive/create', 'parent_id' => $model->id, 'sub_doc' => true]);
                                                    },
                                                    'update' => function ($url,$model) {
                                                        return Html::a(
                                                        '<i class="fa fa-pencil " aria-hidden="true" title = "Редактировать"></i>', 
                                                        ['/abonent/docs-archive/update', 'id' => $model->id, 'sub_doc' => true]);
                                                    },
                                                ],
                                            ];
                    echo GridView::widget($settings); 
                ?>  
<?php Pjax::end() ?>

        </div>
    </div>
</div>