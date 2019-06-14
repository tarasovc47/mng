<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\AddressesRecycle;

$this->title = 'Обработка юр. лиц';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php Pjax::begin(['id' => 'addresses-recycle-pjax']); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
        	'billing_base_clients_client_id',
            'billing_company_name',
            'billing_company_inn',
            'billing_company_address_jur',
            'dadata_clean_address',
            'dadata_suggest_address',
            'postcode',
            //'dadata_suggest_company_name',
            /*[
                'attribute' => 'actual_company',
                'content' => function ($model, $key, $index, $column) {
                    return AddressesRecycle::$actual_status[$model->actual_company];
                }
            ],*/
            /*[
            	'attribute' => 'conclusion_address',
            	'content' => function ($model, $key, $index, $column) {
					return AddressesRecycle::$conclusion[$model->conclusion_address];
				}
            ],
            [
            	'attribute' => 'conclusion_company_name',
            	'content' => function ($model, $key, $index, $column) {
					return AddressesRecycle::$conclusion[$model->conclusion_company_name];
				}
            ],*/
            /*[
                'label' => 'Внести предложенный адрес в биллинг', 
                'content' => function ($model, $key, $index, $column) {
                    return Html::checkbox('rewrite', false, ['class' => 'rewrite_checkbox', 'data-on' => 'Да', 'data-off' => 'Нет', 'data-model-id' => $model->id]);
                }
            ],*/
        ],
    ]); ?>
    <?//= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'addresses-recycle__save']) ?>
<?php Pjax::end(); ?></div>