<?php
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    use yii\widgets\ActiveForm;
    use common\models\ZonesDistrictsAndAreas;
    use common\models\UsersGroups;

    $classes = 'hidden';
    if ((isset(\Yii::$app->request->post('ZonesDistrictsAndAreas')['type']) && (\Yii::$app->request->post('ZonesDistrictsAndAreas')['type'] == 2)) || (isset($model->type) && $model->type == 2)) {
        $classes = '';
    }

    $users_groups = ArrayHelper::map(UsersGroups::find()->where(['department_id'=>2])->all(), 'id', 'name');
?>

<div class="districts-and-areas-form">

    <?php $form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false]); ?>

    <?= $form->field($model, 'name')->textInput() ?>

    <?php 
        $action = \Yii::$app->controller->action->id;
        if ($action == 'create') {
            echo $form->field($model, 'type')->dropDownList($model->types);
        }
    ?>

    <?= $form->field($model, 'parent_id', ['options' => ['class' => $classes]])->dropDownList($model::getDistrictList()) ?>

    <?= $form->field($model, 'users_group_id', ['options' => ['class' => $classes]])->dropDownList($users_groups, ['prompt' => '']) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

