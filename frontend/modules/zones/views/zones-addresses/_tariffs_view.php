<?php 
    use yii\helpers\Html;
    use yii\bootstrap\Modal;
    use yii\widgets\DetailView;
    use common\models\Tariffs;
?>

<?= Html::label('Тарифные планы', null) ?>
<div class="zones__view__tariffs-container">
    <?php foreach ($tariffs_list as $key_tariff => $tariff): ?>
        <div class="thumbnail zones__view__tariff-panel" data-tariff-id="<?= $tariff['id'] ?>">
            <div class="caption">
                <h4><?= $tariff['name'] ?></h4>
                <?php if (!empty($tariff['speed'])): ?>
                    <p>Скорость: <?= $tariff['speed'] ?> мб/с</p>
                <?php endif ?>
                <?php if (!empty($tariff['channels'])): ?>
                    <p>Количество каналов: <?= $tariff['channels'] ?></p>
                <?php endif ?>
                <p>Стоимость: <?= $tariff['price'] ?> руб.</p>
                <p>Сервисы и технологии подключения:</p>
                <p>
                	<?php 
                		$html = '';
                        $html .= '<ul>';
                        foreach ($tariff['services_techs_list'] as $service) {
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

        <?php 
            Modal::begin([
                'header' => '<h2>'.$tariff['name'].'</h2>',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>',
                'closeButton' => [],
                'options' => [
                    'class' => 'zones__view__tariff-modal fade modal',
                    'data' => [
                        'tariff-id' => $tariff['id'],
                    ],
                ],

            ]);
             
            Modal::end();
        ?>
    <?php endforeach ?>
</div>