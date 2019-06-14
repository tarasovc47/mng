<?php
    use yii\helpers\Html;
?>

<?php if (isset($floors_item) && !empty($floors_item)): ?>
    <?php foreach ($floors_item as $key_floor => $floor): ?>

        <div class="zones__address__floor" data-floor-id="<?= $key_floor ?>">
            Этаж <span data-floor-id="<?= $key_floor ?>" class="floor-name"><?= $floor ?></span> 
            <span class="zones__address__floor-control">
                <i class="fa fa-pencil" aria-hidden="true" data-floor-name="<?= $floor ?>" data-floor-id="<?= $key_floor ?>"></i>
                <i class="fa fa-trash" aria-hidden="true" data-floor-name="<?= $floor ?>" data-floor-id="<?= $key_floor ?>"></i>
            </span>
            <div class="zones__address__offices-buttons">
                <?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить офис', ['class' => 'btn btn-link btn-xs zones__address__add-flat', 'data' => ['porch-id' => $porch_id, 'floor-id' => $key_floor, 'room-type' => 2]])
                    .Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить диапазон офисов', ['class' => 'btn btn-link btn-xs zones__address__add-flats', 'data' => ['porch-id' => $porch_id, 'floor-id' => $key_floor, 'room-type' => 2]]) ?>
            </div>
            <div class="zones__address__flats-buttons">
                <?= Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить квартиру', ['class' => 'btn btn-link btn-xs zones__address__add-flat', 'data' => ['porch-id' => $porch_id, 'floor-id' => $key_floor, 'room-type' => 1]])
                    .Html::button('<i class="fa fa-plus" aria-hidden="true"></i> Добавить диапазон квартир', ['class' => 'btn btn-link btn-xs zones__address__add-flats', 'data' => ['porch-id' => $porch_id, 'floor-id' => $key_floor, 'room-type' => 1]]) ?>
            </div>
            <div class="zones__address__flats_offices">
                <div class="zones__address__offices">                                 
                    <?php if (isset($offices_item[$key_floor]) && !empty($offices_item[$key_floor])): ?>
                        <?php foreach ($offices_item[$key_floor] as $key_office => $office): ?>
                            <?= $this->render('____flats_offices', [
                                'flat' => $office,
                                'room_type' => 2,
                                'flat_id' => $key_office,
                            ]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>    
                <div class="zones__address__flats">                                 
                    <?php if (isset($flats_item[$key_floor]) && !empty($flats_item[$key_floor])): ?>
                        <?php foreach ($flats_item[$key_floor] as $key_flat => $flat): ?>
                            <?= $this->render('____flats_offices', [
                                'flat' => $flat,
                                'room_type' => 1,
                                'flat_id' => $key_flat,
                            ]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif ?>