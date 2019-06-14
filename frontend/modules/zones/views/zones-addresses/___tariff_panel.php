<?php
    use yii\helpers\Html;
    use common\models\Tariffs;
?>

<?php if (!empty($tariffs_list)): ?>
    <?php foreach ($tariffs_list as $key_tariff => $tariff): ?>
        <?php 
            if (isset($checked_list[$tariff['id']]) && !empty($checked_list[$tariff['id']])) {
                $checked = true;
                $bg_class = 'bg-success';
            } else {
                $checked = false;
                $bg_class = '';
            }
        ?>
        <div class="thumbnail zones__form__tariff-panel" data-tariff-id="<?= $tariff['id'] ?>" data-abonent-type="<?= $abonent_type?>">
            <div class="caption <?= $bg_class ?>">
                <?= Html::checkbox(
                                    'zones__address__tariff', 
                                    $checked, 
                                    [
                                        'class' => 'manual-tariffs-checkboxes hidden', 
                                        'data' => [
                                            'tariff-id' => $tariff['id'],
                                        ]
                                    ]
                                )
                ?>
                <h4><?= $tariff['name'] ?></h4>
                <p>Стоимость: <?= $tariff['price'] ?> руб.</p>
                <p>Сервисы и технологии подключения:</p>
                <p>
                    <?php 
                        $html = '';
                        $html .= '<ul>';
                        foreach ($tariff['services_and_techs_list'] as $service) {
                            $html .= '<li>'.$service['name'];
                            if (!empty($service['conn_techs'])) {
                                $html .= ':<ul>';
                                foreach ($service['conn_techs'] as $tech) {
                                    $html .= '<li>'.$tech.'</li>';
                                }
                                $html .= '</ul>';
                            }
                            
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                        echo $html;
                    ?>
                </p>
            </div>  
        </div>
    <?php endforeach ?>
<?php endif ?>






