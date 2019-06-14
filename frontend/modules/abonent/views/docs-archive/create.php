<?php

use yii\helpers\Html;

$this->title = 'Создать документ';
if ($abonent) {
   $this->params['breadcrumbs'][] = ['label' => 'Карточка абонента '.$abonent, 'url' => '/abonent/abonent/index?abonent='.$abonent];
   $this->params['breadcrumbs'][] = ['label' => 'Документы абонента '.$abonent, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[abonent]='.$abonent.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
} elseif ($client_id) {
    $this->params['breadcrumbs'][] = ['label' => 'Карточка лицевого счёта '.$client_id, 'url' => '/abonent/abonent/index?client_id='.$client_id];
    $this->params['breadcrumbs'][] = ['label' => 'Документы по лицевому счёту '.$client_id, 'url' => '/abonent/docs-archive/index?DocsArchiveSearch[client_id]='.$client_id.'&&DocsArchiveSearch[parent_id]=-1&DocsArchiveSearch[publication_status]=1'];
}
if ($sub_doc) {
    $this->params['breadcrumbs'][] = ['label' => $parent_file_info['label'], 'url' => '/abonent/docs-archive/view?id='.$parent_file_info['id'].'&DocsArchiveSearch[parent_id]='.$parent_file_info['id'].'DocsArchiveSearch[publication_status]=1'];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="docs-archive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'client_ids' => $client_ids,
        'user_ids' => $user_ids,
        'sub_doc' => $sub_doc,
        'contracts' => $contracts,
    ]) ?>

</div>
