<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Округа и районы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="districts-and-areas-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($this->context->permission == 2): ?>
        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    <?php endif ?>

    <?php 
        switch ($model->type) {
            case '1':
                $view = '_district';  
                break;
            case '2':
                $view = '_area';              
                break;
            default:
                break;
        }

        echo $this->render($view, [
                    'model' => $model,
                    'types' => $types,
                    'addresses' => $addresses,
                ]);
    ?>

</div>

