<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\bootstrap\Modal;
    use common\widgets\AddressSearch;
    use frontend\widgets\MultipleAddressesForm;
?>

<div class="zones-form">

<?php if (isset($extra_data['errors']) && !empty($extra_data['errors'])): ?>
    <div class="zones__address_errors alert alert-danger">
        <ul>
            <?php foreach ($extra_data['errors'] as $group): ?>
                <?php foreach ($group as $error): ?>
                    <li><?php echo $error ?></li>
                <?php endforeach ?>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>  

    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <?php if ($mass_create): ?>
            <li class="zones__address__tab"><a href="#addresses" data-toggle="tab">Выбрать адреса</a></li>
        <?php endif ?>
        <li class="zones__address__tab <?php if (!(\Yii::$app->request->get('scheme'))): ?>active<?php endif ?>"><a href="#information" data-toggle="tab">Общая информация</a></li>
        <li class="zones__address__tab"><a href="#zones__address__individual" data-toggle="tab">Физические лица</a></li>
        <li class="zones__address__tab"><a href="#zones__address__entity" data-toggle="tab">Юридические лица</a></li>
        <?php if (\Yii::$app->controller->action->id == 'update'): ?>
            <li class="zones__address__tab <?php if (\Yii::$app->request->get('scheme')): ?>active<?php endif ?>"><a href="#sсheme" data-toggle="tab">Схема объекта</a></li>
        <?php endif ?>
    </ul>

    <?php $form = ActiveForm::begin(['id' => 'zones__address__create-update', 'enableClientValidation' => false, 'enableAjaxValidation' => false]); ?>

    <div class="zones__address__tab-content tab-content">
        <?php if ($mass_create): ?>
            <div class='tab-pane' data-tab='addresses' id="addresses">
                <?= MultipleAddressesForm::widget([
                        'model' => $model,
                        'attribute' => 'addresses',
                        'template' => 'place-find-editable',
                    ]); 
                ?>
                <?= $form->field($model, 'addresses_stack')->hiddenInput()->label(false)->error(false); ?>
                <div class="form-group">
                    <?= Html::button('Загрузить список адресов из виджета', ['id' => 'zones__address__load-addresses-list', 'class' => 'btn btn-warning', 'disabled' => true]) ?>
                </div>

                <div id="zones__address__addresses-list"></div>
            </div>
        <?php endif ?>

        <div class='tab-pane <?php if (!(\Yii::$app->request->get('scheme'))): ?>active<?php endif ?>' data-tab='information' id="information">
            <?php
                if (!$mass_create) {
                    echo AddressSearch::widget([
                        'model' => $model,
                        'attribute' => "address_uuid",
                        'template' => 'place-editable',
                        'place' => $model->address_uuid,
                    ]);
                }  
            ?>

            <?= $form->field($model, 'district_id')->dropDownList($extra_data['districtsList'], ['prompt' => '']) ?>

            <?= $form->field($model, 'area_id')->dropDownList($extra_data['areasList'], ['prompt' => '', 'disabled' => (empty($model->district_id)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'address_type_id')->dropDownList($extra_data['addressTypesList']) ?>

            <?= $form->field($model, 'manag_company_id')->dropDownList($extra_data['companiesList'], ['prompt' => '']) ?>

            <?= $form->field($model, 'manag_company_branch_id')->dropDownList($extra_data['companyBranchesList'], ['prompt' => '', 'disabled' => (empty($model->manag_company_id)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'key_keeper')->dropDownList($extra_data['keyKeeperList'], ['prompt' => '', 'disabled' => (empty($model->manag_company_id)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'contract_with_manag_company')->checkbox() ?>

            <?= $form->field($model, 'access_agreements')->dropDownList($extra_data['agreementsList'], ['multiple' => 'multiple', 'disabled' => (empty($model->manag_company_id)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'opers')->dropDownList($extra_data['opersList'], ['multiple' => 'multiple']) ?>

            <?php if (!$mass_create): ?>
                <?= $form->field($model, 'coordinates')->textInput() ?>

                <div id="zones__form-map"></div>
            <?php endif ?>

            <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>
        </div>

        <div class='tab-pane zones__address__abonent-type-tab' data-tab='individual' id="zones__address__individual" data-abonent-type="1">
            <?= $form->field($model, 'build_status_individual')->dropDownList($extra_data['statusesList'], ['prompt' => '', 'class' => 'form-control zonesaddresses-build_status', 'data-abonent-type' => '1']) ?>

            <?= $form->field($model, 'services_individual')->dropDownList($extra_data['servicesList'], ['multiple' => 'multiple', 'class' => 'form-control zonesaddresses-services', 'data-abonent-type' => '1', 'disabled' => (empty($model->build_status_individual)) ? 'disabled' : false]) ?>

            <?= $form->field($model, 'conn_techs_individual')->dropDownList($extra_data['connTechsIndividualList'], ['multiple' => 'multiple', 'class' => 'form-control zonesaddresses-conn_techs', 'data-abonent-type' => '1', 'disabled' => (empty($model->services_individual)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'connection_cost_individual')->textarea(['rows' => 6]) ?>

            <div class="zones__addresses__tariffs <?php if (empty($model->conn_techs_individual)): ?> hidden <?php endif ?>" data-abonent-type="1">
                <div class="form-group">
                    <?= Html::checkbox('choise_type', !empty($tariffs['individual']['groups']), ['data-toggle' => 'toggle', 'data-abonent-type' => '1', 'class' => 'zones__addresses__tariffs-choise-type', 'data-on'=>"Групповая привязка тарифов", 'data-off'=>"Единичная привязка тарифов", 'data-onstyle'=>"success", 'data-offstyle'=>"warning", 'data-width' => '250']) ?>
                </div>
                <div class="zones__addresses__tariffs-single-choise <?php if (!empty($tariffs['individual']['groups'])) {echo 'hidden';} ?>" data-abonent-type = "1">
                    <?php
                        echo $form->field($model, 'tariffs_individual')->hiddenInput(['class' => 'form-control zonesaddresses-tariffs', 'data-abonent-type' => '1'])->label(false)->error(false);
                        echo $this->render('__tariffs', [
                            'model' => $model,
                            'abonent_type' => 1,
                            'form' => $form,
                            'conn_techs_list' => $model->conn_techs_individual,
                            'tariffs' => $model->tariffs_individual,
                            'tariffs_list_public' => $extra_data['tariffs_list_individual_public'],
                            'tariffs_list_not_public' => $extra_data['tariffs_list_individual_not_public'],
                        ]);                   
                    ?>
                </div>
                <div class="zones__addresses__tariffs-group-choise <?php if (empty($tariffs['individual']['groups'])) {echo 'hidden';} ?> zones__form__groups-container" data-abonent-type = "1">
                    <?php
                        if(is_array($model->conn_techs_individual) && is_array($tariffs['individual']['groups'])){
                            echo $this->render('__group_panel', [
                                'abonent_type' => 1,
                                'checked_list' => $tariffs['individual']['groups'],
                                'groups_list' => $extra_data['tariffs_list_individual_groups'],
                            ]);  
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class='tab-pane zones__address__abonent-type-tab' data-tab='entity' id="zones__address__entity" data-abonent-type="2">
            <?= $form->field($model, 'build_status_entity')->dropDownList($extra_data['statusesList'], ['prompt' => '', 'class' => 'form-control zonesaddresses-build_status', 'data-abonent-type' => '2']) ?>

            <?= $form->field($model, 'services_entity')->dropDownList($extra_data['servicesList'], ['multiple' => 'multiple', 'class' => 'form-control zonesaddresses-services', 'data-abonent-type' => '2', 'disabled' => (empty($model->build_status_entity)) ? 'disabled' : false]) ?>

            <?= $form->field($model, 'conn_techs_entity')->dropDownList($extra_data['connTechsEntityList'], ['multiple' => 'multiple', 'class' => 'form-control zonesaddresses-conn_techs', 'data-abonent-type' => '2', 'disabled' => (empty($model->services_entity)) ? 'disabled' : false ]) ?>

            <?= $form->field($model, 'connection_cost_entity')->textarea(['rows' => 6]) ?>

            <div class="zones__addresses__tariffs <?php if (empty($model->conn_techs_entity)): ?> hidden <?php endif ?>" data-abonent-type="2">
                <div class="form-group">
                    <?= Html::checkbox(
                            'choise_type', 
                            !empty($tariffs['entity']['groups']), 
                            ['data-toggle' => 'toggle', 'data-abonent-type' => '2', 'class' => 'zones__addresses__tariffs-choise-type', 'data-on'=>"Групповая привязка тарифов", 'data-off'=>"Единичная привязка тарифов", 'data-onstyle'=>"success", 'data-offstyle'=>"warning", 'data-width' => '250']) 
                    ?>
                </div>
                <div class="zones__addresses__tariffs-single-choise <?php if (!empty($tariffs['entity']['groups'])) {echo 'hidden';} ?>" data-abonent-type = "2">
                    <?php
                        echo $form->field($model, 'tariffs_entity')->hiddenInput(['class' => 'form-control zonesaddresses-tariffs', 'data-abonent-type' => '2'])->label(false)->error(false);
                        echo $this->render('__tariffs', [
                            'model' => $model,
                            'abonent_type' => 2,
                            'form' => $form,
                            'conn_techs_list' => $model->conn_techs_entity,
                            'tariffs' => $model->tariffs_entity,
                            'tariffs_list_public' => $extra_data['tariffs_list_entity_public'],
                            'tariffs_list_not_public' => $extra_data['tariffs_list_entity_not_public'],
                        ]);                   
                    ?>
                </div>
                <div class="zones__addresses__tariffs-group-choise <?php if (empty($tariffs['entity']['groups'])) {echo 'hidden';} ?> zones__form__groups-container" data-abonent-type = "2">
                    <?php
                        if(is_array($model->conn_techs_entity) && is_array($tariffs['entity']['groups'])){
                            echo $this->render('__group_panel', [
                                'abonent_type' => 2,
                                'checked_list' => $tariffs['entity']['groups'],
                                'groups_list' => $extra_data['tariffs_list_individual_groups'],
                            ]);  
                        }
                    ?>
                </div>
            </div>
        </div>

        <?php if (\Yii::$app->controller->action->id == 'update'): ?>
            <div class='tab-pane <?php if (\Yii::$app->request->get('scheme')): ?>active<?php endif ?>' data-tab='sсheme' id="sсheme">
                <?= $form->field($model, 'all_flats')->textInput() ?>

                <?= $form->field($model, 'all_offices')->textInput() ?>

                <?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить подъезд', ['class' => 'btn btn-success btn-sm zones__address__add-porch']) ?>

                <div class="zones__address__porches-collapses">
                    <?= $this->render('__porches', [
                            'porches' => $extra_data['porches'],
                        ]);
                    ?>                    
                </div>
            </div>
        <?php endif ?>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success zones__address_submit-button' : 'btn btn-primary zones__address_submit-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if (\Yii::$app->controller->action->id == 'update'): ?>
        <?php 
            Modal::begin([
                'header' => '<h2>Добавить подъезд</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal-add-porch">Добавить подъезд</button>',
                'id' => 'zones__address__modal-add-porch',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::activeLabel($modelPorches, 'porch_name', ['class' => 'control-label'])
                .Html::activeTextInput($modelPorches, 'porch_name', ['class' => 'form-control', 'data' => ['address-id' => $model->id]]).'<div class="help-block"></div></div>'
                .'<div class="form-group">'
                //.Html::dropDownList('porch-template', null, [1, 2], ['class' => 'form-control'])
                .'</div>';
             
            Modal::end();

        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Добавить этаж</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal-add-floor">Добавить этаж</button>',
                'id' => 'zones__address__add-floor',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'.Html::activeLabel($modelFloors, 'floor_name', ['class' => 'control-label']).Html::activeTextInput($modelFloors, 'floor_name', ['class' => 'form-control']).'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Добавить диапазон этажей</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal-add-floors">Добавить диапазон этажей</button>',
                'id' => 'zones__address__add-floors',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::label('C', 'zones__address__modal__floor-begin', ['class' => 'control-label'])
                .Html::input('text', 'floor_begin', '', ['class' => 'form-control', 'id' => 'zones__address__modal__floor-begin'])
                .'<div class="help-block"></div></div>'
                .'<div class="form-group">'
                .Html::label('По', 'zones__address__modal__floor-end', ['class' => 'control-label'])
                .Html::input('text', 'floor_end', '', ['class' => 'form-control', 'id' => 'zones__address__modal__floor-end'])
                .'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Добавить <span class="zones__address__modal-header-flat">квартиру</span><span class="zones__address__modal-header-office hidden">офис</span></h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal-add-flat">Добавить <span class="zones__address__modal-header-flat">квартиру</span><span class="zones__address__modal-header-office hidden">офис</span></button>',
                'id' => 'zones__address__add-flat',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'.Html::label('<span class="zones__address__modal-header-flat">Квартира</span><span class="zones__address__modal-header-office hidden">Офис</span>', 'zonesflats-flat_name', ['class' => 'control-label']).Html::activeTextInput($modelFlats, 'flat_name', ['class' => 'form-control']).'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Добавить диапазон <span class="zones__address__modal-header-flat">квартир</span><span class="zones__address__modal-header-office hidden">офисов</span></h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal-add-flats">Добавить диапазон <span class="zones__address__modal-header-flat">квартир</span><span class="zones__address__modal-header-office hidden">офисов</span></button>',
                'id' => 'zones__address__add-flats',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::label('C', 'zones__address__modal__flat-begin', ['class' => 'control-label'])
                .Html::input('text', 'flat_begin', '', ['class' => 'form-control', 'id' => 'zones__address__modal__flat-begin'])
                .'<div class="help-block"></div></div>'
                .'<div class="form-group">'
                .Html::label('По', 'zones__address__modal__flat-end', ['class' => 'control-label'])
                .Html::input('text', 'flat_end', '', ['class' => 'form-control', 'id' => 'zones__address__modal__flat-end'])
                .'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Редактировать <span class="zones__address__modal-header__update-flat">квартиру</span><span class="zones__address__modal-header__update-office hidden">офис</span></h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal__update-flat">Сохранить</button>',
                'id' => 'zones__address__update-flat',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::label('<span class="zones__address__modal-header__update-flat">Квартира</span><span class="zones__address__modal-header__update-office hidden">Офис</span>', 'zonesflats-flat_name', ['class' => 'control-label'])
                .Html::activeTextInput($modelFlats, 'flat_name', ['class' => 'form-control', 'id' => 'zonesflats-flat_name_update']).'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>

        <?php 
            Modal::begin([
                'header' => '<h2>Редактировать этаж</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal__update-floor">Сохранить</button>',
                'id' => 'zones__address__update-floor',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::label('Этаж', 'zonesfloors-floor_name', ['class' => 'control-label'])
                .Html::activeTextInput($modelFloors, 'floor_name', ['class' => 'form-control', 'id' => 'zonesfloors-floor_name_update']).'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>
        <?php 
            Modal::begin([
                'header' => '<h2>Редактировать подъезд</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                            <button type="button" class="btn btn-primary zones__address__modal__update-porch">Сохранить</button>',
                'id' => 'zones__address__update-porch',
                'closeButton' => [],

            ]);
             
            echo '<div class="form-group">'
                .Html::label('Подъезд', 'zonesporches-porch_name', ['class' => 'control-label'])
                .Html::activeTextInput($modelPorches, 'porch_name', ['class' => 'form-control', 'id' => 'zonesporches-porch_name_update']).'<div class="help-block"></div></div>';
             
            Modal::end();
        ?>
    <?php endif ?>
</div>