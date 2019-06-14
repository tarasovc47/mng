<?php 
use yii\helpers\Html;
?>


<?php $this->beginPage() ?>

<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
	<title><?= Html::encode($this->title) ?></title>
	<meta charset="<?= Yii::$app->charset ?>">

	<?= Html::csrfMetaTags() ?>
<!-- 	<link rel="stylesheet" type="text/css" href="/css/reset.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<script language="javascript" src="/js/jquery-3.1.0.min.js"></script>
<script language="javascript" src="/js/bootstrap.min.js"></script>
<script language="javascript" src="/js/script.js"></script> -->
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
	<div id="container">
		<div id="page404">
			<h1>В доступе отказано, обратитесь к своему руководителю</h1>
			<!-- <a href="/auth/logout">На страницу авторизации</a> -->
		</div>
		<!-- <div id="page404img"><img src="../img/404.jpg"></div> -->
	</div>	
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage(); ?>