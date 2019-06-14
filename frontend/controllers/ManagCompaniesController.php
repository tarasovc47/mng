<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Html;
use common\models\ManagCompanies;
use common\models\ManagCompaniesToContacts;
use common\models\ContactFaces;
use common\models\ZonesAddresses;
use common\models\search\ManagCompaniesSearch;
use common\models\ManagCompaniesBranches;
use common\models\search\ManagCompaniesBranchesSearch;
use frontend\components\FrontendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use common\components\SiteHelper;
use yii\helpers\Json;
use yii\base\DynamicModel;

class ManagCompaniesController extends FrontendComponent
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $publication_status = 0;
        if (isset($_GET["ManagCompaniesSearch"]['publication_status'])) {
            $publication_status = $_GET["ManagCompaniesSearch"]['publication_status'];
        }
        $searchModel = new ManagCompaniesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'publication_status' => $publication_status,
            ]
        );
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        $searchModel = new ManagCompaniesBranchesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $branches = Yii::$app->request->get('from_branch');
        $cas_login = $this->cas_user->login;
        $addresses = ZonesAddresses::getAddressesForManagCompany($model->id);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'branches' => $branches,
            'cas_login' => $cas_login,
            'addresses' => $addresses,
        ]);
    }

    public function actionCreate()
    {
        $model = new ManagCompanies();
        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->publication_status = 1;
            $model->cas_user_id = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect(['view', 
                    'id' => $model->id, 
                    'ManagCompaniesBranchesSearch[company_id]' => $model->id, 
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,
                ]);
            }
        } 
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect(['view', 
                    'id' => $model->id,
                    'ManagCompaniesBranchesSearch[company_id]' => $model->id,
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,
                ]);
            }
        } 
        return $this->render('update', [
            'model' => $model,
        ]);
        
    }

    public function actionRemove($id)
    {
        $model = $this->findModel($id);

        $model->publication_status = 0;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionRecover($id)
    {
        $model = $this->findModel($id);

        $model->publication_status = 1;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->save();

        return $this->redirect(['index']);
    }

    public function actionAddingAddresses($id)
    {
        $modelCompany = $this->findModel($id);
        $dynModel = new DynamicModel(['company_id', 'branch_id', 'key_keeper', 'addresses']);

        $dynModel->addRule(['company_id'], 'required') 
                    ->addRule(['addresses'], 'required', ['message' => 'Необходимо выбрать хотя бы один адрес.']) 
                    ->addRule(['company_id', 'branch_id', 'key_keeper'], 'integer')
                    ->addRule(['addresses'], 'safe');

        if ($dynModel->load(Yii::$app->request->post()) && $dynModel->validate()) {
            foreach ($dynModel->addresses as $key => $address) {
                $address = (int)$address;
                $addressModel = ZonesAddresses::findOne($address);
                if ($addressModel) {
                    $addressModel->manag_company_id = $dynModel->company_id;
                    $addressModel->manag_company_branch_id = $dynModel->branch_id;
                    $addressModel->key_keeper = $dynModel->key_keeper;
                    $addressModel->scenario = 'without_related_values';
                    $addressModel->save();
                }
            }

            return $this->redirect(['view', 
                'id' => $modelCompany->id,
                'ManagCompaniesBranchesSearch[company_id]' => $modelCompany->id,
                'ManagCompaniesBranchesSearch[publication_status]' => 1,
            ]);
        }

        $branches_list = ManagCompaniesBranches::getBranchesList($modelCompany->id);
        $key_keeper_list['УК'] = ManagCompaniesToContacts::getContactsForKeyKeeperList(0, $modelCompany->id);
        return $this->render(
                                'adding_addresses', 
                                [
                                    'modelCompany' => $modelCompany,
                                    'dynModel' => $dynModel,
                                    'branches_list' => $branches_list,
                                    'key_keeper_list' => $key_keeper_list,
                                ]
                            );
    }

    protected function findModel($id)
    {
        if (($model = ManagCompanies::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findContactsModel($id)
    {
        if (($model = ManagCompaniesToContacts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    protected function findBranchesModel($id)
    {
        if (($model = ManagCompaniesBranches::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionCreateContact()
    {
        $model = new ManagCompaniesToContacts();
        $model_contacts = new ContactFaces();
        $company_id = Yii::$app->request->get('company_id');
        if ($company_id == '') {
            return $this->redirect(['index', 'ManagCompaniesSearch[publication_status]' => 1]);
        }
        $branches = Yii::$app->request->get('branches');
        $branch_id = Yii::$app->request->get('branch_id');
        $company = ManagCompanies::findOne($company_id);


        if ($model->load(Yii::$app->request->post())) {
            $save_error = false;
            $errors = array();
            $model->company_id = $company_id;
            $model->cas_user_id = $this->cas_user->id;
            if (!$branches ) {
                $model->branch_id = 0;
            }
            if ($branches && $branch_id) {
                $model->branch_id = $branch_id;
            }
            if ($model->validate() && !empty($model->contact_face_id)) {
                $isset_contact = ManagCompaniesToContacts::issetContactFace($model->contact_face_id, $company_id, $model->branch_id);
                if (!empty($isset_contact)) {
                    $model = $this->findContactsModel($isset_contact['id']);
                    $model->publication_status = 1;
                    $model->updated_at = time();
                    $model->updater = $this->cas_user->id;
                }
            }

            if ($model->save()) {
                if (Yii::$app->request->post('save_and_another')) {
                    return $this->refresh();
                }
                return $this->redirect([
                    'view', 
                    'id' => $company_id, 
                    'from_branch' => $branches ? true : false,
                    'ManagCompaniesBranchesSearch[company_id]' => $company_id,
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,

                ]);
                
            }
        } 
        return $this->render('create_contact', [
            'model' => $model,
            'model_contacts' => $model_contacts,
            'company_id' => $company_id,
            'company' => $company,
            'branches' => $branches,
            'branch_id' => $branch_id,
        ]);
    }

    public function actionUpdateContact($id){
        $model = $this->findContactsModel($id);
        $branches = Yii::$app->request->get('branches');
        $company = ManagCompanies::findOne($model->company_id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect([
                    'view', 
                    'id' => $model->company_id, 
                    'from_branch' => $branches ? true : false,
                    'ManagCompaniesBranchesSearch[company_id]' => $model->company_id,
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,
                ]);
            }
        } 

        return $this->render('update_contact', [
            'model' => $model,
            'company' => $company,
            'branches' => $branches,
        ]);
    }

    public function actionRemoveContact($id)
    {
        $model = $this->findContactsModel($id);
        $branches = Yii::$app->request->get('branches');
        $model->publication_status = 0;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->save();

        return $this->redirect([
            'view', 
            'id' => $model->company_id, 
            'from_branch' => $branches ? true : false,
            'ManagCompaniesBranchesSearch[company_id]' => $model->company_id,
            'ManagCompaniesBranchesSearch[publication_status]' => 1,
        ]);
    }

    public function actionCreateBranch()
    {
        $model = new ManagCompaniesBranches();
        $company_id = Yii::$app->request->get('company_id');
        $company = ManagCompanies::findOne($company_id);

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->company_id = $company_id;
            $model->cas_user_id = $this->cas_user->id;
            $model->publication_status = 1;
            if ($model->save()) {
                return $this->redirect(['view', 
                    'id' => $company_id, 
                    'from_branch' => true, 
                    'ManagCompaniesBranchesSearch[company_id]' => $model->company_id,
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,
                ]);
            }
        } 
        return $this->render('create_branch', [
            'model' => $model,
            'company_id' => $company_id,
            'company' => $company,
        ]);

    }

    public function actionUpdateBranch($id)
    {
        $model = $this->findBranchesModel($id);
        $company_id = Yii::$app->request->get('company_id');
        $company = ManagCompanies::findOne($company_id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updater = $this->cas_user->id;
            if ($model->save()) {
                return $this->redirect([
                    'view', 
                    'id' => $company_id, 
                    'from_branch' => true, 
                    'ManagCompaniesBranchesSearch[company_id]' => $model->company_id,
                    'ManagCompaniesBranchesSearch[publication_status]' => 1,
                ]);
            }
        } 
        return $this->render('update_branch', [
            'model' => $model,
            'company_id' => $company_id,
            'company' => $company,
        ]);
    }

    public function actionRemoveBranch($id)
    {
        $model = $this->findBranchesModel($id);
        $model->publication_status = 0;
        $model->updated_at = time();
        $model->updater = $this->cas_user->id;
        $model->save();

        return $this->redirect([
            'view', 
            'id' => $model->company_id, 
            'from_branch' => true,
            'ManagCompaniesBranchesSearch[company_id]' => $model->company_id,
            'ManagCompaniesBranchesSearch[publication_status]' => 1,
        ]);
    }

    public function actionGetContactsListForBranch(){
        $company_id = Yii::$app->request->get('company_id');
        $branch_id = Yii::$app->request->get('branch_id');
        $contacts_list = ManagCompaniesToContacts::getContactsListForAdding($company_id, $branch_id);
        $html = '';
        foreach ($contacts_list as $key => $contact) {
            $html .= '<option value="'.$key.'">'.$contact.'</option>';
        }

        echo json_encode($html);
        die();
    }

    public function actionGetContactsForBranch(){
        $branch_id = Yii::$app->request->get('branch_id');
        $data['Участок'] = ManagCompaniesToContacts::getContactsForKeyKeeperList($branch_id);

        $html = Html::renderSelectOptions(null, $data);

        echo json_encode($html);
        die();
    }
}
