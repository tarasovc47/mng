<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsOffices */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Должности контактных лиц', 'url' => ['index', 'ContactsOfficesSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contacts-offices-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

        <?= Html::a(($model->publication_status) ? 'Удалить' : 'Восстановить', 
                    null, 
                    [
                        'class' => 'btn btn-danger', 
                        'id' => 'contacts-offices__remove',
                        'data' => [
                            'office-id' => $model->id,
                            'current-status' => $model->publication_status,
                        ],
                    ]) 
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            'comment:ntext',
        ],
    ]) ?>

</div>
