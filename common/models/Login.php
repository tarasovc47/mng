<?php

namespace common\models;

use Yii;
use yii\web\Cookie;
use yii\db\Query;
use common\components\SiteHelper;

class Login extends \yii\db\ActiveRecord
{
    private $sid;               // идентификатор текущей сессии
    private $uid;               // идентификатор текущего пользователя

    public function Login($userCAS){
//        SiteHelper::debug($userCAS);
//        die();
        $login = $userCAS->username;
        $cas_id = $userCAS->id;

        $last_name = $userCAS->givenname;
        $first_name = $userCAS->sn;
        $middle_name = "None";
//        $middle_name = $userCAS->displayname;
//        SiteHelper::debug($userCAS);
        foreach($userCAS->memberof as $role){
            if(mb_substr($role, 0, 4) == "mng_"){
                $department = Departments::findByCasname(mb_substr($role, 4));
                break;
            }
        }


//        $roles = SiteHelper::to_postgre_array($userCAS->memberof);
        $roles = $userCAS->memberof;
//        вытаскиваем пользователя из БД
//        $user = $this->GetByCas($cas_id);

        /*___Временно_____*/
        $userBylogin = $this->GetByLogin($login);
//        echo "---1-----------------------------";

        if(!empty($userBylogin)){
            if($userBylogin->uid==""){
                $userBylogin->uid=$cas_id;
//                echo "---2-----------------------------";
//                SiteHelper::debug($userBylogin);
                $userBylogin->save(false);
            }
        }
//        echo "---3-----------------------------";
        /*^^^Временно^^^^*/
//        SiteHelper::debug($userCAS->memberof);
//        $roles = SiteHelper::to_postgre_array($userCAS->memberof);
        $roles = $userCAS->memberof;
//        SiteHelper::debug($roles);

        $user = $this->GetByCas($cas_id);
//        SiteHelper::debug($user);

        if(!$user){
//            SiteHelper::debug("sadas");
//            die();
//            echo "not found";
            $user = new CasUser();
            $user->login = $login;
            $user->uid = $cas_id;
            $user->roles = $roles;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->middle_name = $middle_name;
            $user->department_id = isset($department->id) ? $department->id : NULL;
            $user->group_id = isset($department->usersGroups[0]->id) ? $department->usersGroups[0]->id : 0;
//            SiteHelper::debug($user);
//            die();
            $user->save(false);

//            echo "---3-----------------------------";
        }else{
//            echo "found";
            $need_update = false;
            if(!($user->login == $login) || !($user->last_name == $last_name) || !($user->first_name == $first_name) || !($user->middle_name == $middle_name)){
                $need_update = true;
            }

            if($roles != $user->roles){
                $need_update = true;
            }

            if(!$user->group_id){
                $need_update = true;
            }


            if($need_update){
                $user->login = $login;

                $user->roles = $roles;

                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->middle_name = $middle_name;
                $user->department_id = isset($department->id) ? $department->id : NULL;

                if(isset($department->usersGroups[0]->id) && !$user->group_id){
                    $user->group_id = $department->usersGroups[0]->id;
                }

//                SiteHelper::debug($user);
//                die();
                $user->save(false);
//                echo "update";
//                SiteHelper::debug($roles);
//                SiteHelper::debug($user);
//                die();
            }
        }


        // запоминаем имя сессия - 8 часов
        $expire = time() + 60 * 60 * 8;
//        $expire = time() + 8;
//        (new Cookie)->expire = $expire;


        Yii::$app->response->cookies->add(new Cookie([
            'name' => 'cas_id',
            'value' => $cas_id,
            'expire' => $expire,
            'domain' => Yii::$app->params['domain'],
            'secure' => false,
            'httpOnly' => true,
            'path' => "/",
        ]));

        // открываем сессию и запоминаем SID
        if ($user->id!=null) {
            $this->sid = $this->OpenSession($user->id);
        }else{
            SiteHelper::debug($user);
//            echo $user;
            die();
        }

        return $user;
    }

    public function Logout(){
        Yii::$app->response->cookies->remove(new Cookie([
            'name' => 'cas_id',
            'domain' => Yii::$app->params['domain'],
        ]));
        unset($_SESSION['sid']);
        $this->sid = null;
        $this->uid = null;
        Yii::$app->user->logout();
    }

    public function Get($id_user = null){
        // Если id_user не указан, берем его по текущей сессии.
        if($id_user == null)
            $id_user = $this->GetUid();

        if($id_user == null)
            return null;

        // А теперь просто возвращаем пользователя по id_user.
        return CasUser::findOne($id_user);
    }

    public function GetUid(){
        // Проверка кеша.
        if ($this->uid != null)
            return $this->uid;

        // Берем по текущей сессии.
        $sid = $this->GetSid();

        if ($sid == null)
            return null;

        $result = Yii::$app->db->createCommand('SELECT id_user FROM sessions WHERE sid = :sid')
            ->bindValue(':sid', $sid)
            ->queryOne();

        // Если сессию не нашли - значит пользователь не авторизован.
        if (!$result)
            return null;

        // Если нашли - запоминм ее.
        $this->uid = $result['id_user'];
        return $this->uid;
    }

    private function GetSid(){
        // Проверка кеша.
        if($this->sid != null)
            return $this->sid;

        // Ищем SID в сессии.
        $sid = null;
        if(isset($_SESSION['sid'])) {
            $sid = $_SESSION['sid'];
        }

        // Если нашли, попробуем обновить time_last в базе.
        // Заодно и проверим, есть ли сессия там.
        if($sid != null){
            $time_last = time();

            $affected_rows = Yii::$app->db->createCommand()
                ->update('sessions', ['time_last' => $time_last], "sid = '$sid'")
                ->execute();

            if($affected_rows == 0){
                $result = Yii::$app->db->createCommand('SELECT count(*) FROM sessions WHERE sid = :sid')
                    ->bindValue(':sid', $sid)
                    ->queryScalar();

                if($result == 0)
                    $sid = null;
            }
        }


        // Нет сессии?
        // Т.е. пробуем переподключиться.
        if($sid == null && Yii::$app->request->cookies->getValue('cas_id')){
            $user = $this->GetByCas(Yii::$app->request->cookies->getValue('cas_id'));

            if($user){
                if ($user->id!=null) {
                    $sid = $this->OpenSession($user->id);
                }else{
                    SiteHelper::debug($user);
                    die();
                }
            }
        }

        // Запоминаем в кеш.
        if($sid != null)
            $this->sid = $sid;

        // Возвращаем, наконец, SID.
        return $sid;
    }

    public function GetByCas($cas_id){
        return CasUser::find()->where(["uid" => $cas_id])->one();
    }

    public function GetByLogin($login){
        return CasUser::find()->where(["login" => $login])->one();
    }


    private function OpenSession($id_user){
        // генерируем SID
        $sid = $this->GenerateStr(10);

        // вставляем SID в БД
        $now = time();

        Yii::$app->db->createCommand()
            ->insert('sessions', [
                'id_user' => $id_user,
                'sid' => $sid,
                'time_start' => $now,
                'time_last' => $now,
            ])
            ->execute();

        // регистрируем сессию в PHP сессии
        $_SESSION['sid'] = $sid;

        // возвращаем SID
        return $sid;
    }

    private function GenerateStr($length = 10){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;

        while (strlen($code) < $length)
            $code .= $chars[mt_rand(0, $clen)];

        return $code;
    }
}
