<?
use yii\widgets\Breadcrumbs;
use common\components\SiteHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

Pjax::begin();
echo Breadcrumbs::widget([
    'itemTemplate' => "<li>{link}</li>\n", // template for all links
    'links' => [
        [
            'label' => 'ARP таблицы',
            'url' => '/ipmon/arptables',
            'template' => "<li>{link}</li>\n", // template for this link only
        ],

        'Нeт доступа',

    ],
]);?>

    <div class="alert alert-danger" >

        <i class='fa fa-2x fa-lock pull-left'></i>
        <b class=' text-bold'>Доступ закрыт</b>
        <small>Нужно разрешение!</small>
        <br>
        <i class='text-muted'>Нет доступа!</i>
    </div>
<?
Pjax::end();