<?php
    use yii\helpers\Html;
    use common\widgets\Attributes as AttributesWidget;
?>
<div class="departments-properties">
    <? if($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать атрибут', ['/properties/create'], ['class' => 'btn btn-success']) ?>
            <?//= Html::button('Сохранить сортировку', ['class' => 'btn btn-info save-sort']) ?>
        </p>
    <? endif ?>
    <div class="properties-tree<? if($this->context->permission == 2): ?> has-access<? endif ?>">
        <? foreach($properties as $type): ?>
			<div class="panel panel-primary">
                <div class="panel-heading type">
                    <a data-toggle="collapse" href="#type_<?= $type['id']; ?>"><?= $type['name'] ?></a>
                </div>
                <div id="type_<?= $type['id']; ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?= $this->render('_properties_tree', [
                                'properties' => $type["properties"],
                                'editAccess' => ($this->context->permission == 2),
                                'classes' => 'properties-tree__list first-lvl',
                            ]);
                        ?>
                    </div>
                </div>
            </div>
        <? endforeach ?>
    </div>
</div>