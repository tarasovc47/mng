<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\AddressSearch;
use common\models\Operators;
use common\models\ZonesDistrictsAndAreas;
use common\models\ManagCompanies;
use common\models\ZonesAddressTypes;

?>

<div class="zones-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
    ]); ?>

     <?= AddressSearch::widget([
                'model' => $model,
                'attribute' => "address_uuid",
                'template' => 'place-find-editable',
            ]); ?>

    <?= $form->field($model, 'address_type_id')->dropDownList(ZonesAddressTypes::getAddressTypesList(), ['prompt' => '']) ?>

    <?
        $district_list = ZonesDistrictsAndAreas::getDistrictList();
        unset($district_list[-1]);
        echo $form->field($model, 'district_id')->dropDownList($district_list, ['prompt' => '']) 
    ?>

    <?= $form->field($model, 'area_id')->dropDownList(ZonesDistrictsAndAreas::getAreasList(), ['prompt' => '']) ?>

    <?= $form->field($model, 'manag_company_id')->dropDownList(ManagCompanies::getCompaniesList(), ['prompt' => '']) ?>
    
    <?= $form->field($model, 'opers')->dropDownList(Operators::loadList(), ['prompt' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Искать', ['id' => 'zones__search__search-button', 'class' => 'btn btn-primary', 'disabled' => 'disabled']) ?>
        <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-default tariffs__reset-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
