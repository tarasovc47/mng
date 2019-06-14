<?php

use yii\helpers\Html;

$this->title = 'Профиль';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile">
	<?php echo $userLeftMenu; ?>
    <h1 class="page-header"><?= Html::encode($this->title) ?></h1>
</div>


