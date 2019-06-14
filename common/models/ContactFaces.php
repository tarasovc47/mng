<?php

namespace common\models;

use Yii;
use yii\helpers\Html;
use common\components\SiteHelper;
use common\models\ContactFacesPhones;
use common\models\ContactFacesEmails;
use common\models\history\ContactFacesHistory;

class ContactFaces extends \yii\db\ActiveRecord
{
    public $phones;
    public $phones_comments;
    public $emails;
    public $emails_comments;
    public $manag_companies;
    public $updated_at;
    public $updater;

    public static function tableName()
    {
        return 'contact_faces';
    }

    public function getContactFacesEmails()
    {
        return $this->hasMany(ContactFacesEmails::className(), ['contact_face_id' => 'id'])->where('publication_status = 1');
    }

    public function getContactFacesPhones()
    {
        return $this->hasMany(ContactFacesPhones::className(), ['contact_face_id' => 'id'])->where('publication_status = 1');
    }

    public function getManagCompanies()
    {
        return $this->hasMany(ManagCompanies::className(), ['id' => 'company_id'])->where('publication_status = 1')
                    ->viaTable(ManagCompaniesToContacts::tableName(), ['contact_face_id' => 'id'], 
                        function($query) {
                        $query->where(['publication_status' => 1]);
                    });
    }

    public function beforeValidate(){
        if (!empty($this->phones) && is_array($this->phones)) {
            foreach ($this->phones as $key => $phone) {
                if ($phone != '') {
                    $this->phones[$key] = SiteHelper::clearPhone($phone);
                } else {
                    unset($this->phones[$key]);
                }
            }
        } else {
            $this->phones = SiteHelper::clearPhone($this->phones);
        }
        if (!empty($this->emails) && is_array($this->emails)) {
            foreach ($this->emails as $key => $email) {
                if ($email == '') {
                    unset($this->emails[$key]);
                }
            }
        }

        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            
            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes){
        parent::afterSave($insert, $changedAttributes);

        $this->processingRelations($this->phones, ContactFacesPhones::className(), 'getContactPhones', 'phone', $this->phones_comments);
        $this->processingRelations($this->emails, ContactFacesEmails::className(), 'getContactEmails', 'email', $this->emails_comments);

        $history = new ContactFacesHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public function rules()
    {
        return [
            [['name', 'phones', 'created_at', 'cas_user_id', 'publication_status'], 'required'],
            [['created_at', 'cas_user_id', 'publication_status', 'updated_at', 'updater'], 'integer'],
            [['name', 'comment'], 'string'],
            [['name', 'comment'], 'trim'],
            [['phones'], 'each', 'rule' => ['string', 'length' => 10]],
            [['phones_comments', 'emails_comments'], 'each', 'rule' => ['string']],
            [['phones_comments', 'emails_comments', 'phones'], 'each', 'rule' => ['trim']],
            [['emails'], 'each', 'rule' => ['email']],

            [['phones'], 'each', 'rule' => ['validatePhonesUnique'], 'skipOnError' => false, 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'phones' => 'Номер телефона',
            'emails' => 'Электронная почта',
            'comment' => 'Примечание',
            'phones_comments' => 'Примечание',
            'emails_comments' => 'Примечание',
            'created_at' => 'Created At',
            'cas_user_id' => 'Cas User ID',
            'publication_status' => 'Publication Status',
        ];
    }

    public function validatePhonesUnique($attribute, $params){
        $contact_id = $this->id ? $this->id : false;
        $phone_isset = ContactFacesPhones::issetPhone($this->phones, $contact_id);

        if (isset($phone_isset) && !empty($phone_isset)) {
            foreach ($phone_isset as $key => $phone) {
                $number = SiteHelper::handsomePhone($phone['phone']);
                $contact = Html::a(self::findOne($phone['contact_face_id'])['name'], '/contact-faces/view?id='.$phone['contact_face_id'], ['target'=>'_blank']);
                $this->addError($attribute, 'Телефонный номер '. $number  .' уже существует. Принадлежит контактному лицу: '.$contact );
            }
        }
    }

    public static function find()
    {
        return new \common\models\query\ContactFacesQuery(get_called_class());
    }

    public function getRelatedValues(){
        $this->phones = ContactFacesPhones::getContactPhones($this->id);
        $this->emails = ContactFacesEmails::getContactEmails($this->id);
        $this->phones_comments = ContactFacesPhones::getContactPhonesComments($this->id);
        $this->emails_comments = ContactFacesEmails::getContactEmailsComments($this->id);
        return;
    }

    protected function processingRelations($data, $model_name, $method_name, $column_name, $comments)
    {
        $old_data = $model_name::$method_name($this->id);

        if (!empty($data)) {
            if (is_array($data)) {
                foreach ($data as $key => $value) {
                    if (in_array($value, $old_data)) {
                        unset($old_data[array_search($value, $old_data)]);
                        $model = $model_name::findOne([$column_name => $value, 'contact_face_id' => $this->id]);
                        $model->publication_status = 1;
                        $model->updated_at = $this->updated_at;
                        $model->updater = $this->updater;
                        $model->save();
                    } else {
                        $model = new $model_name();
                        $model->contact_face_id = $this->id;
                        $model->comment = $comments[$key];
                        $model->$column_name = $value;
                        $model->publication_status = 1;
                        $model->created_at = $this->created_at;
                        $model->cas_user_id = $this->cas_user_id;
                        $model->save();
                    }
                }
            } else {
                if (in_array($data, $old_data)) {
                    unset($old_data[array_search($data, $old_data)]);
                    $model = $model_name::findOne([$column_name => $data, 'contact_face_id' => $this->id]);
                    $model->publication_status = 1;
                    $model->updated_at = $this->updated_at;
                    $model->updater = $this->updater;
                    $model->save();
                } else {
                    $model = new $model_name();
                    $model->contact_face_id = $this->id;
                    $model->comment = $comments[$key];
                    $model->$column_name = $data;
                    $model->publication_status = 1;
                    $model->created_at = $this->created_at;
                    $model->cas_user_id = $this->cas_user_id;
                    $model->save();
                }
            }
        }

        if (!empty($old_data)) {
            foreach ($old_data as $key => $value) {
                $model = $model_name::findOne([$column_name => $value, 'contact_face_id' => $this->id]);
                $model->publication_status = 0;
                $model->updated_at = $this->updated_at;
                $model->updater = $this->updater;
                $model->save();
            }
        }
    }
}
