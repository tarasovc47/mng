<?php 

use yii\widgets\DetailView;
use common\models\ZonesDistrictsAndAreas;
use yii\helpers\Html;

?>

<?php 

    echo DetailView::widget([
    	'model' => $model,
        'attributes' => [
            'name',
            [
                'label' => 'Тип',
                'value' => $types[$model->type],
            ],
            [
	            'label' => 'Районы',
	            'format' => 'html',
	            'value' => function($model, $widget){
	                $areas = ZonesDistrictsAndAreas::getAreasListByDistrict($model->id);
	                $html = '';
	                foreach ($areas as $key => $area) {
	                    $html .= Html::a($area, ['view', 'id' => $key]).'<br>';
	                }
	                return $html;
	            },
	        ],
	        'comment:ntext',
        ],
    ]);
?>