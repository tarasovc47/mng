<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DocsArchive */

$this->title = 'Редактировать документ: ' . $model->label;
if ($model->abonent) {
   $this->params['breadcrumbs'][] = ['label' => 'Карточка абонента '.$model->abonent, 'url' => '/abonent/abonent/index?abonent='.$model->abonent];
   $this->params['breadcrumbs'][] = ['label' => 'Документы абонента '.$model->abonent, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[abonent]='.$model->abonent.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
} elseif ($model->client_id) {
    $this->params['breadcrumbs'][] = ['label' => 'Карточка лицевого счёта '.$model->client_id, 'url' => '/abonent/abonent/index?client_id='.$model->client_id];
    $this->params['breadcrumbs'][] = ['label' => 'Документы по лицевому счёту '.$model->client_id, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[client_id]='.$model->client_id.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
}
$this->params['breadcrumbs'][] = ['label' => $model->label, 'url' => ['view', 'id' => $model->id, 'DocsArchiveSearch[parent_id]' => $model->id, 'sub_doc' => $sub_doc, 'DocsArchiveSearch[publication_status]' => 1]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="docs-archive-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'sub_doc' => $sub_doc,
        'client_ids' => $client_ids,
        'user_ids' => $user_ids,
        'contracts' => $contracts,
    ]) ?>

</div>
