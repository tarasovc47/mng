<?php

namespace backend\controllers;

use Yii;
use common\models\search\DepartmentsSearch;
use backend\components\BackendComponent;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\SearchFields;
use common\models\SearchFieldsSettings;


class SearchSettingsController extends BackendComponent
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
        $searchModel = new DepartmentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionUpdate($id)
    {
        $department = $id;
        $fields = SearchFields::getFieldsList();
        $department_fields_values = SearchFieldsSettings::getValuesList($department, 0);

        return $this->render('update', [
            'department' => $department,
            'fields' => $fields,
            'department_fields_values' => $department_fields_values,
        ]);
    }
}
