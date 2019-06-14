<?php
    use yii\helpers\Html;

    
    echo '<div class="form-group">'
        .Html::checkbox(
            'all-active-tariffs__'.$conn_tech->id, 
            $checked, 
            [
                'class' => 'all-active-tariffs-toggle all-active-tariffs', 
                'data' => [
                    'on' => 'Да', 
                    'off' => 'Нет',
                    'conn-tech-id' => $conn_tech->id,
                    'abonent-type' => $abonent_type,
                ]
            ]
        )
        .' '.$conn_tech->name.' ('.$conn_tech->service->name.')'
        .'</div>';
      
?>

