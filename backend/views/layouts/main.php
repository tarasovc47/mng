<?php

    use backend\assets\AppAsset;
    use yii\helpers\Html;
    use yii\bootstrap\Nav;
    use yii\bootstrap\NavBar;
    use yii\widgets\Breadcrumbs;
    use common\widgets\Alert;
    use yii\bootstrap\Modal;

    AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="<?= $this->context->htmlClasses; ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
        <div class="wrap">
            <?php
                NavBar::begin([
                    'options' => [
                        'class' => 'navbar-inverse navbar-fixed-top',
                    ],
                ]);
                echo Nav::widget([
                    'options' => ['class' => 'navbar-nav'],
                    'items' => $this->context->menuTopItems,
                ]);
                NavBar::end();
            ?>
            <div class="container">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <?= Alert::widget() ?>
                <?= $content ?>
            </div>
        </div>
        <?php
            Modal::begin([
                'header' => '<h4>Подтвердите действие</h4>',
                'footer' => Html::button('Подтвердить', ["class" => "btn btn-success"]) . 
                    Html::button('Отмена', ["class" => "btn btn-default cancel", "data-dismiss" => "modal"]),
                'id' => 'confirm',
                'closeButton' => false,
            ]);
        ?>
            <div class="confirm-content"></div>
        <?php Modal::end(); ?>
        <?php
            Modal::begin([
                'header' => '<h4>Уведомление</h4>',
                'footer' => Html::button('Закрыть', ["class" => "btn btn-default", "data-dismiss" => "modal"]),
                'id' => 'notice',
                'options' => [
                    'data-backdrop' => 'static',
                    'data-keyboard' => 'false',
                ],
                'closeButton' => false,
            ]);
        ?>
            <div class="notice-content"></div>
        <?php Modal::end(); ?>
        <div id="loader"></div>
        <div id="loader-content">
            <i class="fa fa-spinner fa-spin fa-3x"></i>
        </div>
    <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>