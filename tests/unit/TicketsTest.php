<?php

namespace common\tests\unit\models;

use Yii;
use common\models\Tickets;
//use common\fixtures\Tickets as TicketsFixture;

/**
 * Login form test
 */
class TicketsTest extends \Codeception\Test\Unit
{
    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;


    public function _before()
    {
        /*$this->tester->haveFixtures([
            'user' => [
                'class' => TicketsFixture::className(),
                'dataFile' => codecept_data_dir() . 'ticket.php'
            ]
        ]);*/
    }



    public function testSaveNoName()
    {
        $model = new Tickets([
            'name' => '',
            'start_on' => 'dsfdf',
            'planned_start_on' => 'sssss',
        ]);

        expect('model should not be saved', $model->save())->false();
    }

   /* public function testLoginWrongPassword()
    {
        $model = new LoginForm([
            'username' => 'bayer.hudson',
            'password' => 'wrong_password',
        ]);

        expect('model should not login user', $model->login())->false();
        expect('error message should be set', $model->errors)->hasKey('password');
        expect('user should not be logged in', Yii::$app->user->isGuest)->true();
    }

    public function testLoginCorrect()
    {
        $model = new LoginForm([
            'username' => 'bayer.hudson',
            'password' => 'password_0',
        ]);

        expect('model should login user', $model->login())->true();
        expect('error message should not be set', $model->errors)->hasntKey('password');
        expect('user should be logged in', Yii::$app->user->isGuest)->false();
    }*/
}
