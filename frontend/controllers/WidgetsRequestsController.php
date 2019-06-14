<?php

namespace frontend\controllers;

use Yii;
use common\models\ZonesAddresses;
use common\components\SiteHelper;

class WidgetsRequestsController extends \frontend\components\FrontendComponent
{
    public function actionMultipleAddresses()
    {
        $addresses = Yii::$app->request->get('addresses');
        $addresses = SiteHelper::to_php_array($addresses);

        $data = array();
        $data = ZonesAddresses::addressesSearch($addresses);

        if (!empty($data)) {
        	$html = Yii::$app->controller->renderPartial('@frontend/widgets/views/multiple-addresses-form/_table', 
                                [
                                    'addresses' => $data,
                                ]);
        } else {
            $html = '<div class="alert alert-warning">Не найдено ни одного адреса.</div>';
        }
        echo json_encode($html);
        die();
    }

}
