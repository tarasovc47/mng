<?php
    use yii\helpers\Html;
?>
<div class="departments-groups">
    <? if($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Создать группу', [ '/users-groups/create', 'department' => $department->id ], [ 'class' => 'btn btn-success' ]) ?>
            <?= Html::button('Сохранить распределение по группам', [ 'class' => 'btn btn-info save-groups' ]) ?>
        </p>
    <? endif ?>
    <? if($undefined_users): ?>
        <div class="panel panel-default undefined">
            <div class="panel-heading">
                Пользователи без группы. В идеале этой панели быть не должно, при входе в систему они автоматически будут перемещены в первую подходящую группу. Или можете сделать это сейчас самостоятельно.
            </div>
            <div class="panel-body">
                <ol class="groups__users-list undefined">
                    <? foreach($undefined_users as $user): ?>
                        <li data-id="<?= $user->id; ?>"><?= $user->last_name . " " . $user->first_name ?></li>
                    <? endforeach ?>
                </ol>
            </div>
        </div>
    <? endif ?>
    <? foreach($groups as $group): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                Группа «<?= $group->name; ?>»
                <a href="/users-groups/update?id=<?= $group->id; ?>" title="Редактировать">
                    <i class="fa fa-pencil"></i>
                </a>
            </div>
            <div class="panel-body">
                <ol class="groups__users-list" data-id="<?= $group->id; ?>">
                    <? foreach($group->casUsers as $user): ?>
                        <li data-id="<?= $user->id; ?>"><?= $user->last_name . " " . $user->first_name ?></li>
                    <? endforeach ?>
                </ol>
            </div>
        </div>
    <? endforeach ?>
</div>