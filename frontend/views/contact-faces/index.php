<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\ContactFacesPhones;
use common\models\ContactFacesEmails;
use common\components\SiteHelper;

$this->title = 'Контактные лица';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="contact-faces-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать контактное лицо', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


<?php Pjax::begin(); ?>
    <p>
        <?= Html::checkbox('show_only_publicated', $publication_status ? true : false, ['label' => 'Показывать только активные', 'class' => 'contact-faces__show-only-publicated']) ?>
    </p>

<?php
$script = <<< JS
$(document).ready(function() {
$('.contact-faces__show-only-publicated').click(function(){
var e = $.Event("keydown", {keyCode: 13});
var value = $('.contact-faces__show-only-publicated').prop('checked') ? 1 : '';
$('.contact-faces__publication-status').val(value).trigger(e);
});
});
JS;
$this->registerJs($script);
?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'name',
                'content' => function ($model, $key, $index, $column){
                        return Html::a($model->name, ['view', 'id' => $model->id], ['title' => 'Просмотр']);
                    },
            ],
            [
                'attribute' => 'phones',
                'content' => function ($model, $key, $index, $column){
                        $html = '';
                        $phones = ContactFacesPhones::getContactPhones($model->id);
                        foreach ($phones as $key => $phone) {
                            $html .= '<p>'.SiteHelper::handsomePhone($phone).'</p>';
                        }
                        return $html;
                    },
            ], 
            [
                'attribute' => 'emails',
                'content' => function ($model, $key, $index, $column){
                        $html = '';
                        $emails = ContactFacesEmails::getContactEmails($model->id);
                        foreach ($emails as $key => $email) {
                            $html .= '<p>'.$email.'</p>';
                        }
                        return $html;
                    },
            ], 
            /*[
                'attribute' => 'manag_companies',
                'content' => function ($model, $key, $index, $column){
                        $html = '';
                        $emails = ContactFacesEmails::getContactEmails($model->id);
                        foreach ($emails as $key => $email) {
                            $html .= '<p>'.$email.'</p>';
                        }
                        return $html;
                    },
            ], */
            'comment:ntext',
            [
                'attribute' => 'publication_status',
                'filterInputOptions' => [
                    'class' => 'contact-faces__publication-status hidden'
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
                'class' => 'yii\grid\ActionColumn', 
                'template' => '{update} {remove-recover}',
                'buttons' => [
                                'remove-recover' => function ($url, $model, $key){
                                if ($model->publication_status) {
                                    return Html::a('', ['remove', 'id' => $model->id], 
                                    [
                                        'title' => 'Удалить', 
                                        'class' => 'glyphicon glyphicon-trash',
                                        'data' => [
                                            'confirm' => 'Вы уверены что хотите удалить данное контактное лицо?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                } else {
                                    return Html::a('', ['recover', 'id' => $model->id], 
                                    [
                                        'title' => 'Восстановить', 
                                        'class' => 'glyphicon glyphicon-plus',
                                        'data' => [
                                            'confirm' => 'Уверены что хотите восстановить данное контактное лицо?',
                                            'method' => 'post',
                                        ],
                                    ]);
                                }
                        
                        
                    },
                ],
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
