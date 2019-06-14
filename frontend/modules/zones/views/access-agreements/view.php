<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Operators;
use common\models\ManagCompanies;

/* @var $this yii\web\View */
/* @var $model common\models\ZonesAccessAgreements */

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => 'Договоры доступа', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zones-access-agreements-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
    	<?php if ($this->context->permission == 2): ?>
    		<?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    	<?php endif ?>
        
        <?= Html::a('Скачать', [$model->name], ['class' => 'btn btn-info', 'download' => true]) ?>
    </p>

    <?php 
        $rent = ($model->price_is_ratio) ? ' (в процентном соотношении от количества пользователей)' : '';
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'label',
                [
                    'label' => 'Оператор',
                    'value' => function ($model, $widget){
                        return Operators::findOne($model->oper_id)['name'];                           
                    },
                ],
                [
                    'label' => 'Управляющая компания',
                    'value' => function ($model, $widget){
                        return ManagCompanies::findOne($model->manag_company_id)['name'];                           
                    },
                ],
                [
                    'label' => 'Дата заключения',
                    'value' => date('d-m-Y', $model->opened_at),
                ],
                [
                    'label' => 'Автоматическая пролонгация',
                    'value' => ($model->auto_prolongation) ? 'Да' : 'Нет',
                ],
                [
                    'label' => 'Действует до',
                    'value' => ($model->closed_at != 0) ? date('d-m-Y', $model->closed_at) : '',
                ],
                [
                    'label' => 'Стоимость аренды'.$rent,
                    'value' => $model->rent_price.' руб.',
                ],
                'comment:ntext',
            ],
        ]);
    ?>

    <div class="zones__document-view">
    <?php if ($model->extension != null && $model->extension == 'pdf'): ?>
        <h3>Просмотр документа</h3>
        <iframe src="<?php echo $model->name ?>"></iframe>
    <?php endif ?>
    </div>

</div>
