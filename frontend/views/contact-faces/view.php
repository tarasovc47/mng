<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\components\SiteHelper;

/* @var $this yii\web\View */
/* @var $model common\models\ContactFaces */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Контактные лица', 'url' => ['index', 'ContactFacesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-faces-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['remove', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены что хотите удалить данное контактное лицо?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?php 
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                [
                    'label' => 'Номер телефона',
                    'format' => 'html',
                    'value' => function ($model, $widget){
                        $phones_html = '';
                        foreach ($model->contactFacesPhones as $key => $phone) {
                            $phones_html .= '<p>'.SiteHelper::handsomePhone($phone['phone']);
                            if (!empty($phone['comment'])) {
                                $phones_html .= ' ('.$phone['comment'].')';
                            }
                            $phones_html .= '</p>';
                        }
                        return $phones_html;
                    }
                    
                ],
                [
                    'label' => 'Электронная почта',
                    'format' => 'html',
                    'value' => function ($model, $widget){
                        $emails_html = '';
                        foreach ($model->contactFacesEmails as $key => $email) {
                            $emails_html .= '<p>'.$email['email'];
                            if (!empty($email['comment'])) {
                                $emails_html .= ' ('.$email['comment'].')';
                            }
                            $emails_html .= '</p>';
                        }
                        return $emails_html;
                    }
                ],
                [
                    'label' => 'Управляющие компании',
                    'format' => 'html',
                    'value' => function ($model, $widget){
                        $html = '';
                        foreach ($model->managCompanies as $key => $company) {
                            $html .= Html::a($company['name'], ['/manag-companes/view', 'id' => $company['id']]);
                            $html .= '<br>';
                        }
                        return $html;
                    }
                ],
                'comment:ntext',
            ],
        ]);
    ?>

</div>
