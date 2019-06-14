<?php
    use yii\helpers\Html;
?>

<?php if (!empty($groups_list)): ?>
    <?php foreach ($groups_list as $key_group => $group): ?>
        <?php 
            if (in_array($group['id'], $checked_list)) {
                $checked = true;
                $bg_class = 'bg-success';
            } else {
                $checked = false;
                $bg_class = '';
            }
        ?>
        <div class="thumbnail zones__form__group-panel" data-group-id="<?= $group['id'] ?>" data-abonent-type="<?= $abonent_type?>">
            <div class="caption <?= $bg_class ?>">
                <?= Html::checkbox(
                                    'zones__address__group', 
                                    $checked, 
                                    [
                                        'class' => 'manual-group-checkboxes hidden', 
                                        'data' => [
                                            'group-id' => $group['id'],
                                        ]
                                    ]
                                )
                ?>
                <h4><?= $group['name'] ?></h4>
                <p>
                    <?php foreach ($group->tariffsToGroups as $key => $tariff): ?>
                        <?= $tariff->tariff->name ?><br>
                    <?php endforeach ?>
                </p>
            </div>  
        </div>
    <?php endforeach ?>
<?php endif ?>




