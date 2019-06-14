<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\components\SiteHelper;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    public function attributeLabels()
    {
        return [
            'username'=>'Имя пользователя',
            'password'=>'Пароль',
            'rememberMe'=>'Запомнить'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePasswordLdap'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    public function validatePasswordLdap($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUserLdap();
            if (!$user || !Yii::$app->ad->auth()->attempt($this->username,$this->password)) {
                $this->addError($attribute, 'Incorrect username or passwords.');
            }
        }

    }


    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
//            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 60*60*8);
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 60*60*8 : 0);
        }

        return false;
    }

    public function loginLdap()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUserLdap(), $this->rememberMe ? 3600 * 24 * 30 : 60*60*8);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {

            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }

    public function getUserLdap()
    {
        if ($this->_user === null) {
            $this->_user = User::findIdentity($this->username);
        }
        return $this->_user;
    }
}
