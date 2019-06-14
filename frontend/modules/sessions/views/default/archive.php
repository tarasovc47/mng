<?php
use yii\grid\GridView;
/**
 * Created by PhpStorm.
 * User: asmild
 * Date: 03.04.18
 * Time: 10:14
 */


echo GridView::widget([
    'dataProvider' => $DataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'login',
        'ipv4_addr',
        'ipv6_prefix',
        'mac_addr',
        'started_at',
        'stopped_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);