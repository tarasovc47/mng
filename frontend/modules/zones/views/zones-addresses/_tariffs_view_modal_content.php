<?php 
    use Yii;
    use yii\helpers\Html;
    use yii\widgets\DetailView;

    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => 'Приоритетный тариф',
                'format' => 'html',
                'value' => $model->priority_tariff[$model->priority],
            ],
            [
                'label' => 'Общедоступный тариф',
                'format' => 'html',
                'value' => $model->public_tariff[$model->public],
            ],
            [
                'label' => 'Пакетный тариф',
                'format' => 'html',
                'value' => $model->package_tariff[$model->package],
            ],
            [
                'label' => 'Операторы',
                'format' => 'html',
                'value' =>  function ($model, $widget){
                    $html = '';
                    foreach ($model->tariffsToOpers as $operator) {
                        $html .= $operator->operators->name.'<br>';
                    }
                    return $html;
                },
            ],
            [
                'label' => 'Сервисы и технологии подключения',
                'format' => 'html',
                'value' => function ($model, $widget) use ($extra_data){
                    $html = '';
                    $html .= '<ul class="tariffs__view__services">';
                    foreach ($extra_data['services_and_techs_list'] as $service) {
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
                    return $html;
                },
            ],
            [
                'label' => 'Тип абонента',
                'value' => Yii::$app->params['abonent_types'][$model->for_abonent_type],
            ],
            [
                'label' => 'Тариф в биллинге',
                'format' => 'html',
                'value' => function ($model, $widget){
                    $html = '';
                    if (isset($model->billing_id) && !empty($model->billing_id)) {
                        foreach ($model->billing_id as $id => $billing_id) {
                            $html .= $billing_id . '<br>';
                        }
                    }
                    return $html;
                },
            ],
            [
                'label' => 'Дата открытия',
                'value' => date('d-m-Y', $model->opened_at),
            ],
            [
                'label' => 'Дата закрытия',
                'value' => ($model->closed_at != '') ? date('d-m-Y', $model->closed_at) : '',
            ],
            'price',
            'comment:ntext',
        ],
    ]);