<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use common\widgets\AttributesTree;

$this->title = 'Модуль «Техподдержка» :: Атрибуты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="techsup-attributes-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <? if($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать атрибут', ['create'], ['class' => 'btn btn-success']) ?>
            <?= Html::button('Сохранить сортировку', ['class' => 'btn btn-info save-sort']) ?>
        </p>
    <? endif ?>
    <div class="techsup-attributes-tree<? if($this->context->permission == 2): ?> has-access<? endif ?>">
        <? foreach($tree as $serviceId => $service): ?>
            <div class="techsup-attributes-tree__service">
                <div class="service-name"><strong><?= $service['name'] ?></strong></div>
                <? foreach($service['techs'] as $techId => $tech): ?>
                    <div class="techsup-attributes-tree__conn-tech">
                        <div class="conn-tech-name"><strong><?= $tech['name'] ?></strong></div>
                        <?= AttributesTree::widget([
                            'attrs' => $tech['attrs'],
                            'template' => "backend/techsup/attributes/index",
                            'editAccess' => ($this->context->permission == 2),
                        ]) ?>
                    </div>
                <? endforeach ?>
            </div>
        <? endforeach ?>
    </div>
</div>
