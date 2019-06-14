<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\DocsArchive;
use common\models\DocsTypes;
use yii\helpers\ArrayHelper;
use yii\jui\DatePicker;

if ($abonent) {
   $this->title = 'Документы абонента '.$abonent;
   $this->params['breadcrumbs'][] = ['label' => 'Карточка абонента '.$abonent, 'url' => '/abonent/abonent/index?abonent='.$abonent];
} elseif ($client_id) {
    $this->title = 'Документы по лицевому счёту '.$client_id;  
    $this->params['breadcrumbs'][] = ['label' => 'Карточка лицевого счёта '.$client_id, 'url' => '/abonent/abonent/index?client_id='.$client_id];
}


$this->params['breadcrumbs'][] = $this->title;
?>
<?php echo $abonentLeftMenu; ?>
<div class="docs-archive-index"> 
    
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php 
        $url = ['create'];
        if ($abonent) {
           $url['abonent'] = $abonent;
        } elseif ($client_id) {
            $url['client_id'] = $client_id;
        }

        echo Html::a('Создать документ', $url, ['class' => 'btn btn-success']) ?>
    </p>
    
    <?php
        if ($abonent) {
           $client_ids = DocsArchive::getClientIDs($abonent);
           
        }
        $settings = [
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'label',
                                'format' => 'html',
                                'value' => function ($model){
                                    return Html::a($model->label, [
                                        'view', 
                                        'id' => $model->id,
                                        'DocsArchiveSearch[parent_id]' => $model->id,
                                        'DocsArchiveSearch[publication_status]' => 1,
                                    ]);
                                }

                            ],
                            [
                                'attribute' => 'parent_id',
                                'filterInputOptions' => [
                                    'class' => 'manag-companies__publication-status hidden'
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
                               'filter' => DocsTypes::getDocTypesList(0),
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

        if($abonent){
            $settings['columns'][] = [
                                        'attribute' => 'client_id',
                                        'filter' => $client_ids,
                                        'content' => function ($model, $key, $index, $column){
                                            return $column->filter[$model->client_id];
                                        }
                                    ];
        }
                                    
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
                                            ['/abonent/docs-archive/create', 'sub_doc' => 1, 'parent_id' => $model->id]);
                                        },
                                    ],
                                ];


                

        echo GridView::widget($settings); ?>  
</div>