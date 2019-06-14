<?php 
	use common\components\SiteHelper;
	use yii\helpers\Html;
?>
<tr>
    <td><?= Html::a('<i class="fa fa-times" aria-hidden="true"></i>', null, ['class' => 'addresses-list__delete', 'data-uuid' => $uuid]) ?>
    </td>
    <td><?= SiteHelper::getAddressNameByUuid($uuid) ?></td>
</tr>