<?php
	use yii\helpers\Html;
	use yii\widgets\ActiveForm;
	use common\models\ManagCompaniesBranches;
    use common\models\ManagCompaniesToContacts;
	use common\models\ContactsOffices;
	use yii\bootstrap\Modal;
?>



<?php 
	$form = ActiveForm::begin(['enableClientValidation' => false, 'enableAjaxValidation' => false]); 
	 
	$contacts_list = array();
    $contact_offices_list = array();
    $contact_offices_list = ContactsOffices::getOfficesList();
    $disabled_contact_list = false;
    if ($model->isNewRecord) {
        if (!$branches) {
            $contacts_list = ManagCompaniesToContacts::getContactsListForAdding($company_id, false);
        } 
        if ($branches && !$branch_id) {
            $branchesList = ManagCompaniesBranches::getBranchesList($company_id);
            echo $form->field($model, 'branch_id')->dropDownList($branchesList, ['prompt' => '', 'data' => ['company-id' => $company_id]]);
            $disabled_contact_list = true;
        }
        if ($branch_id && empty($model->branch_id)) {
            $contacts_list = ManagCompaniesToContacts::getContactsListForAdding($company_id, $branch_id);
            $disabled_contact_list = false;
        }
        if (!empty($model->branch_id)) {
            $contacts_list = ManagCompaniesToContacts::getContactsListForAdding($company_id, $model->branch_id);
            $disabled_contact_list = false;
        }
    }
?>

<div class="manag-companies__contacts-from-list">
	<?php
        if ($model->isNewRecord) {
            echo $form->field($model, 'contact_face_id')->dropDownList($contacts_list, ['disabled' => $disabled_contact_list]);
        } 
    ?>
    <?= $form->field($model, 'contact_office_id')->dropDownList($contact_offices_list, ['disabled' => $disabled_contact_list]) ?>
    <?= $form->field($model, 'comment')->textArea(['row' => 6]) ?>
</div>

<div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>

    <?php
        if ($model->isNewRecord) {
            echo Html::submitButton('Сохранить и добавить ещё', ['name' => 'save_and_another', 'class' => 'btn btn-success']);
        } 
    ?>

    <?= Html::a('Отменить', ['view', 'id' => $company_id, 'from_branch' => $branches ? true : false, 'ManagCompaniesBranchesSearch[publication_status]' => 1, 'ManagCompaniesBranchesSearch[company_id]' => $company_id,], ['class' => 'btn btn-default']) ?>
</div>

<?php ActiveForm::end(); ?>
