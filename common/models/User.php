<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Adldap\Adldap;
use common\components\SiteHelper;
use common\models\Login;
use common\models\CasUser;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * {@inheritdoc}
     */
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public $memberof;
    public $username;
    public $displayname;
    public $givenname;
    public $sn;
    public $mail;
    public $id;
    protected static $cas_user;
    /**
     * {@inheritdoc}
     */
    /*
     * //original
     public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }*/

    private static function SIDtoString($ADsid)
    {
        $sid = "S-";
        //$ADguid = $info[0]['objectguid'][0];
        $sidinhex = str_split(bin2hex($ADsid), 2);
        // Byte 0 = Revision Level
        $sid = $sid.hexdec($sidinhex[0])."-";
        // Byte 1-7 = 48 Bit Authority
        $sid = $sid.hexdec($sidinhex[6].$sidinhex[5].$sidinhex[4].$sidinhex[3].$sidinhex[2].$sidinhex[1]);
        // Byte 8 count of sub authorities - Get number of sub-authorities
        $subauths = hexdec($sidinhex[7]);
        //Loop through Sub Authorities
        for($i = 0; $i < $subauths; $i++) {
            $start = 8 + (4 * $i);
            // X amount of 32Bit (4 Byte) Sub Authorities
            $sid = $sid."-".hexdec($sidinhex[$start+3].$sidinhex[$start+2].$sidinhex[$start+1].$sidinhex[$start]);
        }
        return $sid;
    }

    public static function findIdentity($id)
    {
        $new_identity = new User();
        if ($user_ldap_info = Yii::$app->ad->search()->select(['cn','sn', 'samaccountname', 'telephone', 'mail','memberof','uidnumber','objectsid','displayname','givenname'])->findBy('sAMAccountname',$id)){
            $user_ldap_info->cas_id = User::SIDtoString($user_ldap_info->objectsid[0]);
            $model = new Login();
            $new_identity->setId($user_ldap_info->cas_id[0]);
            $new_identity->setEmail($user_ldap_info->mail[0]);
            $new_identity->setUsername($user_ldap_info->samaccountname[0]);
            $new_identity->setDisplayName($user_ldap_info->displayname[0]);
            $new_identity->setGivenname($user_ldap_info->givenname[0]);
            $new_identity->setSn($user_ldap_info->sn[0]);
            $new_identity->setMemberOf($user_ldap_info->memberof);
            User::$cas_user = $model->Login($new_identity);
//            SiteHelper::debug($user_ldap_info);
//            SiteHelper::debug($new_identity);

        }
//        die();
        return $new_identity;
    }

    protected function setEmail($email)
    {
        $this->mail = $email;
    }
    protected function setDisplayName($name)
    {
        $this->displayname = $name;
    }
    protected function setSn($sn)
    {
        $this->sn = $sn;
    }

    protected function setUsername($username)
    {
        $this->username = $username;
    }
    protected function setGivenname($givenname)
    {
        $this->givenname = $givenname;
    }

    protected function setId($id)
    {
        $this->id = $id;
    }

    protected function setMemberOf($groups)
    {
        foreach ($groups as $group){
            $group = str_replace("CN=","",explode(",",$group)[0]);
            $this->memberof[] = $group;
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        /* $ad = new Adldap();
         $config = [
             // An array of your LDAP hosts. You can use either
             // the host name or the IP address of your host.
             'hosts'    => ['10.60.248.4'],

             // The base distinguished name of your domain to perform searches upon.
             'base_dn'  => 'dc=rkdom,dc=t72,dc=ru',

             // The account to use for querying / modifying LDAP records. This
             // does not need to be an admin account. This can also
             // be a full distinguished name of the user account.
             'username' => 'administrator@rkdom.t72.ru',
             'password' => '77RusscoM77',
         ];

         $ad->addProvider($config);
         $provider = $ad->connect();
         $ldapObject = $provider->search()->find($username);
         $user = new User;
         $user->username = $ldapObject['samaccountname'][0];
         return $user;
 //        $user = $username.'@rkdom.t72.ru';
 //        $password = 'QAfn2@31';
 //        if ($provider->auth()->attempt($user, $password)) {
 //            echo "pass";// Passed.
 //            try {
                 // If a successful connection is made to your server, the provider will be returned.
                 // Performing a query.
 //    $results = $provider->search()->where('cn', '=', 'Ildar')->get();
                 // Finding a record.
 //                $ldapObject = $provider->search()->find($username);
 //                $givenName = $ldapObject['givenname'][0];
 //                $surname = $ldapObject['surname'][0];
 //                $displayname = $ldapObject['displayname'][0];
 //                $telephone = $ldapObject['telephonenumber'][0];
 //                $mail = $ldapObject['mail'][0];
 //
 //                echo 'gn: ' . $givenName . '<br> sn: ' . $surname .
 //                    '<br> dispname: ' . $displayname . '<br> phone: ' . $telephone.'<br> mail '.$mail;
 //                echo '<br>';
 //                echo 'Руководитель '.$ldapObject['manager'][0];
 //                SiteHelper::debug($ldapObject['memberof']);
                 // Creating a new LDAP entry. You can pass in attributes into the make methods.
 //    $user =  $provider->make()->user([
 //        'cn'          => 'John Doe',
 //        'title'       => 'Accountant',
 //        'description' => 'User Account',
 //    ]);

                 // Setting a model's attribute.
 //    $user->cn = 'John Doe';

                 // Saving the changes to your LDAP server.
 //    if ($user->save()) {
 //         User was saved!
 //    }
 //            } catch (\Adldap\Auth\BindException $e) {

                 // There was an issue binding / connecting to the server.

 //            }
 //        } else {
 //            echo "fail";
 //             Failed.
 //        }

         die();*/
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    /*public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }*/

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->username;
//        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return 0;
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}
