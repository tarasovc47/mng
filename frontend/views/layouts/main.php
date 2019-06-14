<?php
	use yii\helpers\Html;
	use yii\widgets\Menu;
	use yii\widgets\Breadcrumbs;
	use yii\bootstrap\Nav;
	use frontend\assets\AppAsset;
	use common\components\SiteHelper;
	use common\widgets\LeftMenu;
	use yii\bootstrap\Modal;

AppAsset::register($this);
dmstr\web\AdminLteAsset::register($this);
//yii\bootstrap4\BootstrapAsset::register($this);
//$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/yiisoft/bootstrap4/dist');
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="<?= $this->context->htmlClasses; ?>">
	<head>
	    <meta charset="<?=Yii::$app->charset?>">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <?= Html::csrfMetaTags() ?>
	    <title><?= Html::encode($this->title) ?></title>
        <style>


            #anchor-header:before,
            #anchor-header  {
                content: url('/img/icons/hydra_logo.png') ;
            /*background-position: 10px 10px; !* X and Y *!*//*
            vertical-align: middle;
            background-size: contain;
            height: 1.2em;
            width: 1.2em;
        }
</style>
	    <?php $this->head() ?>
	</head>
	<body class="hold-transition skin-blue sidebar-mini">
		<?php $this->beginBody() ?>
		<div class="wrapper">
            <?= $this->render(
                'header.php',
                ['directoryAsset' => $directoryAsset]
            ) ?>

            <?= $this->render(
                'left.php',
                ['directoryAsset' => $directoryAsset,
                 'menuLeftItems'=>$this->context->menuLeftItems ]
            )
            ?>

            <?= $this->render(
                'content.php',
                ['content' => $content, 'directoryAsset' => $directoryAsset]
            ) ?>

		</div>
		<?php
			Modal::begin([
			    'header' => '<h4>Подтвердите действие</h4>',
			    'footer' => Html::button('Подтвердить', ["class" => "btn btn-success"]) . 
			    			Html::button('Отмена', ["class" => "btn btn-default cancel", "data-dismiss" => "modal"]),
			    'id' => 'confirm',
			    'options' => [
			    	'data-backdrop' => 'static',
			    	'data-keyboard' => 'false',
			    ],
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