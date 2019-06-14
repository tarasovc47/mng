<?php
    use yii\helpers\Html;
    use common\widgets\Attributes as AttributesWidget;
?>
<div class="departments-attributes">
    <? if($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать атрибут', ['/attributes/create', 'department' => $department->id], ['class' => 'btn btn-success']) ?>
            <?= Html::button('Сохранить сортировку', ['class' => 'btn btn-info save-sort']) ?>
        </p>
    <? endif ?>
    <div class="attributes-tree<? if($this->context->permission == 2): ?> has-access<? endif ?>">
        <? foreach($attributes as $service): ?>
            <div class="panel panel-primary">
                <div class="panel-heading service">
                    <a data-toggle="collapse" href="#<?= $service['machine_name']; ?>"><?= $service['name'] ?></a>
                </div>
                <div id="<?= $service['machine_name']; ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <? foreach($service["connection_technologies"] as $technology): ?>
                            <div class="panel panel-info">
                                <div class="panel-heading technology">
                                    <a data-toggle="collapse" href="#technology_<?= $technology['id']; ?>"><?= $technology['name'] ?></a>
                                </div>
                                <div id="technology_<?= $technology['id']; ?>" class="panel-collapse collapse">
                                    <div class="panel-body">
                                        <?= $this->render('_attributes_tree', [
                                                'attributes' => $technology['attributes'],
                                                'editAccess' => ($this->context->permission == 2),
                                                'classes' => 'attributes-tree__list first-lvl',
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
        <? endforeach ?>
    </div>
</div>