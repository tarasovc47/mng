<?php 

use yii\widgets\DetailView;
use common\models\ZonesDistrictsAndAreas;
use yii\helpers\Html;
use common\models\UsersGroups;
use common\components\SiteHelper;

?>

<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="areas_tab active"><a href="#information" data-toggle="tab">Общая информация</a></li>
    <li class="areas_tab"><a href="#addresses" data-toggle="tab">Обслуживаемые адреса</a></li>
</ul>
<div class="tab-content tab-content">
	<div class='tab-pane active' data-tab='information' id="information">
		<?= DetailView::widget([
		    	'model' => $model,
		        'attributes' => [
		            'name',
		            [
		                'attribute' => 'type',
		                'value' => $types[$model->type],
		            ],
		        	[
		                'attribute' => 'parent_id',
		                'format' => 'html',
		                'value' => function($model, $widget){
		                    $district = ZonesDistrictsAndAreas::getDistrict($model->parent_id);
		                    return Html::a($district['name'], ['view', 'id' => $district['id']]);
		                },
		            ],
		            [
		                'attribute' => 'users_group_id',
		                'value' => UsersGroups::findOne($model->users_group_id)['name'],
		            ],
		            'comment:ntext',
		        ],
		    ]);
		?>
	</div>
	<div class='tab-pane' data-tab='addresses' id="addresses">
		<?php if ($this->context->permission == 2): ?>
			<p><?= Html::a('Привязать ещё адреса', ['adding-addresses', 'id' => $model->id], ['class' => 'btn btn-primary']) ?></p>
		<?php endif ?>
		
            <table class="table table-striped table-bordered">
                <tbody>
                <?php foreach ($addresses as $key_address => $address): ?>
                    <tr>
                        <td>
                            <?= Html::a(SiteHelper::getAddressNameByUuid($address), ['/zones/zones-addresses/view', 'id' => $key_address], ['class' => 'address_link']); ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table> 
	</div>
</div>