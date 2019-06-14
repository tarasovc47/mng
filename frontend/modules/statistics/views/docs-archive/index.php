<?php 
    use common\models\DocsArchive;

    $this->title = 'Статистика по архиву документов';
    $this->params['breadcrumbs'][] = $this->title;
?>


<div class="statistics-default-index">
    <h1><?= $this->title ?></h1>

    <div class="well">
        Всего документов загружено: <?= $statistics['all'] ?>
    </div>

    <div class="well">
        Всего документов загружено за текущий месяц: <?= $statistics['all_current_month'] ?>
    </div>

    <div class="well">
        <table class="table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Документов загружено за текущий месяц</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($statistics['users_current_month'] as $key => $user): ?>
                    <tr>
                        <td><?= DocsArchive::getOneCasUser($user['cas_user_id']) ?></td>
                        <td><?= $user['count'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
