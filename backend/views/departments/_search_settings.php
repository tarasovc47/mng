<?php

use yii\helpers\Html;
use common\models\Access;
?>
<div class="departments-search-settings" data-department-id="<?= $department_id ?>">
    <div class="departments-search-settings-form">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Отображение столбцов в результатах поиска</h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <td>Название поля</td>
                                <td class="search-settings__checkboxes">Отображать</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Номер абонента</td>
                                <td class="search-settings__checkboxes">
                                    <?= Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Лицевой счёт</td>
                                <td class="search-settings__checkboxes">
                                    <?= Html::checkbox('disabled', true, ['disabled' => 'disabled']); ?>
                                </td>
                            </tr>

                            <?php foreach ($fields as $key_field => $field): ?>
                                <tr>
                                    <td class="search-settings__labels" data-field-id="<?= $field['id'] ?>"><?=Html::label($field['label'], 'search-settings__checkbox__'.$field['id']) ?></td>
                                    <td class="search-settings__checkboxes">
                                        <?php
                                        $checked = $field['display_default_setting'];
                                        if (isset($department_fields_values[$field['id']])) {
                                            $checked = $department_fields_values[$field['id']];
                                        }
                                        echo Html::checkbox(
                                                $field['id'], 
                                                $checked, 
                                                [
                                                    'id' => 'search-settings__checkbox__'.$field['id'],
                                                    'class' => 'search-settings__checkbox',
                                                    'data' => [
                                                        'field-id' => $field['id'],
                                                    ],
                                                ]
                                            ) 
                                        ?>                              
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>

                <?= Html::button('Сохранить', ['class' => 'btn btn-success', 'id' => 'search-settings__save']) ?>

                <div class="alert alert-success" id="search-settings__success">Изменения сохранены <i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i></div>
                <div class="alert alert-danger" id="search-settings__danger">Что-то пошло не так, ничего не сохранилось <i class="fa fa-times-circle-o fa-lg" aria-hidden="true"></i></div>

            </div>
        </div>
    </div>
</div>