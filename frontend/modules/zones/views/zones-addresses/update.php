<?php

use yii\helpers\Html;
use common\components\SiteHelper;

$this->title = 'Редактировать адрес: ' . SiteHelper::getAddressNameByUuid($model->address_uuid);
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => SiteHelper::getAddressNameByUuid($model->address_uuid), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="zones-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'extra_data' => $extra_data,
        'modelPorches' => $modelPorches,
        'modelFloors' => $modelFloors,
        'modelFlats' => $modelFlats,
        'mass_create' => false,
        'tariffs' => $tariffs,
    ]) ?>

</div>
