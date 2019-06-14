<?php

use yii\helpers\Html;
use common\components\SiteHelper;
use yii\bootstrap\Alert;

$this->title = 'Массовое создание адресов';
$this->params['breadcrumbs'][] = ['label' => 'Адреса', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Массовое создание адресов';
?>
<div class="zones-mass-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    	if (isset($message) && !empty($message)) {
	    	echo Alert::widget([
			    'options' => [
			        'class' => 'alert-success',
			    ],
			    'body' => $message,
			]); 
	    }
	?>

    <?= $this->render('_form', [
    	'model' => $model,
    	'extra_data' => $extra_data,
    	'mass_create' => true,
    ]) ?>

</div>
