<?php
    use yii\helpers\Html;
    use yii\helpers\Json;
    use yii\helpers\ArrayHelper;
    use common\models\ConnectionTechnologies;

    $tariffs = Json::decode($tariffs, true);
?>

<div class="all-active-tariffs-toggle-group">
    <div class="form-group">
        <?= Html::label('Автоматически подключать все активные тарифные планы', null, ['class' => 'control-label']) ?>
    </div>

    <?php
        if (isset($conn_techs_list) && !empty($conn_techs_list)) {
            foreach ($conn_techs_list as $conn_tech) {
                $conn_tech = ConnectionTechnologies::findOne($conn_tech);
                echo $this->render('___conn_tech_checkbox', [
                    'conn_tech' => $conn_tech,
                    'abonent_type' => $abonent_type,
                    'checked' => (isset($tariffs['auto_tariffs'][$conn_tech->id]) && !empty($tariffs['auto_tariffs'][$conn_tech->id])) ? true : false,
                ]);  
            }
        }
    ?>
</div> 



<div class="all-active-tariffs-panel-group <?php if (count($conn_techs_list) <= count($tariffs['auto_tariffs'])) {
    echo 'hidden';
} ?>">
    <div class="form-group">
        <?= Html::label('Выбрать тарифные планы вручную', null, ['class' => 'control-label']) ?>
    </div>

    <div class="zones__form__tariffs-container zones__form__public-tariffs" data-abonent-type="<?= $abonent_type ?>">
        <?php
            if(is_array($conn_techs_list) && is_array($tariffs['auto_tariffs'])){
                if (count($conn_techs_list) > count($tariffs['auto_tariffs'])) {
                    echo $this->render('___tariff_panel', [
                        'abonent_type' => $abonent_type,
                        'checked_list' => $tariffs['manual_tariffs'],
                        'tariffs_list' => $tariffs_list_public,
                    ]);
                }     
            }
        ?>
    </div>
</div> 

<div class="all-active-not-public-tariffs-panel-group <?php if (empty($tariffs_list_not_public)) {
    echo 'hidden';
} ?>">
    <div class="form-group">
        <?= Html::label('Выбрать дополнительные тарифные планы', null, ['class' => 'control-label']) ?>
    </div>

    <div class="zones__form__tariffs-container zones__form__not-public-tariffs" data-abonent-type="<?= $abonent_type ?>">
        <?php 
            if(is_array($conn_techs_list) && is_array($tariffs['auto_tariffs'])){
                if (!empty($tariffs_list_not_public)) {
                    echo $this->render('___tariff_panel', [
                        'abonent_type' => $abonent_type,
                        'checked_list' => $tariffs['manual_tariffs'],
                        'tariffs_list' => $tariffs_list_not_public,
                    ]); 
                }
            }
        ?>
    </div>
</div>

