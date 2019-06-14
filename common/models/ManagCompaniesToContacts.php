<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\SiteHelper;
use common\models\ContactFacesPhones;
use common\models\ContactFacesEmails;
use common\models\history\ManagCompaniesToContactsHistory;

class ManagCompaniesToContacts extends \yii\db\ActiveRecord
{
    public $updated_at;
    public $updater;
    
    public static function tableName()
    {
        return 'manag_companies_to_contacts';
    }

    public function getContactFaces()
    {
        return $this->hasOne(ContactFaces::className(), ['id' => 'contact_face_id']);
    }

    public function rules()
    {
        return [
            [['company_id', 'branch_id', 'created_at', 'cas_user_id', 'publication_status', 'contact_face_id', 'contact_office_id'], 'required'],
            [['company_id', 'branch_id', 'created_at', 'cas_user_id', 'publication_status', 'contact_face_id', 'contact_office_id', 'updated_at', 'updater'], 'integer'],
            [['comment'], 'string'],
            [['comment'], 'trim'],
        ];
    }

    public function beforeValidate(){
        if($this->isNewRecord){
            $this->created_at = time();
            $this->publication_status = 1;
        }

        return parent::beforeValidate();
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Управляющая компания',
            'branch_id' => 'Филиал',
            'contact_face_id' => 'Контактное лицо',
            'contact_office_id' => 'Должность',
            'comment' => 'Примечание',
            'created_at' => 'Дата создания', 
            'cas_user_id' => 'Создал',
            'publication_status' => 'Статус публикации',
        ];
    }

    public static function find()
    {
        return new \common\models\query\ManagCompaniesToContactsQuery(get_called_class());
    }

    public function afterSave($insert, $changedAttributes){
        $history = new ManagCompaniesToContactsHistory();
        $history->setAttributes($this->getAttributes());
        $history->origin_id = $this->id;
        if (!$insert) {
           $history->created_at = $this->updated_at;
           $history->cas_user_id = $this->updater;
        }
        $history->save();
    }

    public static function getContactsListForAdding($company_id, $branch){   
        if (!$branch) {
            $where = "cf.id NOT IN (SELECT contact_face_id FROM manag_companies_to_contacts WHERE company_id = '" . $company_id ."' AND branch_id = 0 AND publication_status = 1)";
        } else {
            $where = "cf.id NOT IN (SELECT contact_face_id FROM manag_companies_to_contacts WHERE branch_id = '" . $branch ."'  AND publication_status = 1)";
        }
        $connection = Yii::$app->db;
        $contacts =  $connection
                    ->createCommand("
                        SELECT cf.id, cf.name, cf.comment
                        FROM contact_faces cf
                        WHERE cf.publication_status = 1 AND ".$where."
                        GROUP BY cf.id, cf.name
                        ORDER BY id ASC
                    ")
                    ->queryAll();

        $contacts = ArrayHelper::index($contacts, 'id');

        foreach ($contacts as $key => $contact) {
            $phones = $connection
                    ->createCommand("
                        SELECT phone
                        FROM contact_faces_phones
                        WHERE publication_status = 1 AND contact_face_id = '{$key}'
                    ")
                    ->queryColumn();
            $emails = $connection
                    ->createCommand("
                        SELECT email
                        FROM contact_faces_emails
                        WHERE publication_status = 1 AND contact_face_id = '{$key}'
                    ")
                    ->queryColumn();
            foreach ($phones as $key_phone => $phone) {
                $phones[$key_phone] = SiteHelper::handsomePhone($phone);
            }
            $contacts[$key] = $contact['name'].' ('.implode(', ', $phones);
            if (!empty($emails)) {
                $contacts[$key] .= '; '.implode(', ', $emails);
            }
            if (!empty($contact['comment'])) {
                $contacts[$key] .= '; '.$contact['comment'];
            }
            $contacts[$key] .= ')';
                            
        }
        
        return $contacts;
    } 

    public static function getCompanyContactsForView($company_id, $branch_id = 0){
        $connection = Yii::$app->db;
        $contacts =  $connection
                    ->createCommand("
                        SELECT mnc.id, cf.id as contact_id, cf.name, cf.comment as common_comment, mnc.comment as private_comment, co.name as office, mnc.contact_face_id
                        FROM contact_faces cf
                        LEFT JOIN manag_companies_to_contacts mnc ON cf.id = mnc.contact_face_id
                        LEFT JOIN contacts_offices co ON co.id = mnc.contact_office_id
                        WHERE mnc.company_id = '".$company_id."' AND mnc.publication_status = 1 AND mnc.branch_id = '{$branch_id}'
                        GROUP BY mnc.id, cf.name, cf.comment, mnc.comment, co.name, cf.id
                        ORDER BY id ASC
                    ")
                    ->queryAll();

        foreach ($contacts as $key => $contact) {
            $contacts[$key]['phones'] = ContactFacesPhones::getContactPhones($contact['contact_face_id']);
            foreach ($contacts[$key]['phones'] as $key_phone => $phone) {
                $contacts[$key]['phones'][$key_phone] = SiteHelper::handsomePhone($phone);
            }
            $contacts[$key]['phones'] = implode(', ', $contacts[$key]['phones']);

            $contacts[$key]['emails'] = ContactFacesEmails::getContactEmails($contact['contact_face_id']);
            $contacts[$key]['emails'] = implode(', ', $contacts[$key]['emails']);

            $contacts[$key]['comments'] = '';
            if (!empty($contact['common_comment'])) {
                $contacts[$key]['comments'] .= $contact['common_comment'];
            }
            if (!empty($contacts[$key]['comments'])) {
                $contacts[$key]['comments'] .= '; ';
            }
            if (!empty($contact['private_comment'])) {
                $contacts[$key]['comments'] .= $contact['private_comment'];
            }
            
        }
        return $contacts;
    }

    public static function getOneContact($branch_id, $contact_id){
        $connection = Yii::$app->db;
        $contact =  $connection
                    ->createCommand("
                        SELECT mnc.id, cf.id as contact_id, cf.name, cf.comment as common_comment, mnc.comment as private_comment, co.name as office, mnc.contact_face_id
                        FROM contact_faces cf
                        LEFT JOIN manag_companies_to_contacts mnc ON cf.id = mnc.contact_face_id
                        LEFT JOIN contacts_offices co ON co.id = mnc.contact_office_id
                        WHERE mnc.contact_face_id = '{$contact_id}' AND mnc.branch_id = '{$branch_id}'
                    ")
                    ->queryOne();

       
        $contact['phones'] = ContactFacesPhones::getContactPhones($contact['contact_face_id']);
        foreach ($contact['phones'] as $key_phone => $phone) {
            $contact['phones'][$key_phone] = SiteHelper::handsomePhone($phone);
        }
        $contact['phones'] = implode(', ', $contact['phones']);

        $contact['emails'] = ContactFacesEmails::getContactEmails($contact['contact_face_id']);
        $contact['emails'] = implode(', ', $contact['emails']);

        $contact['comments'] = '';
        if (!empty($contact['common_comment'])) {
            $contact['comments'] .= $contact['common_comment'];
        }
        if (!empty($contact['comments'])) {
            $contact['comments'] .= '; ';
        }
        if (!empty($contact['private_comment'])) {
            $contact['comments'] .= $contact['private_comment'];
        }
            
        
        return $contact;
    }

    public static function issetContactFace($contact_face_id, $company_id, $branch_id = 0){
        $connection = Yii::$app->db;
        return  $connection
                    ->createCommand("
                        SELECT id
                        FROM manag_companies_to_contacts  
                        WHERE contact_face_id = '{$contact_face_id}' 
                            AND company_id = '{$company_id}' 
                            AND branch_id = '{$branch_id}'
                    ")
                    ->queryOne();
    }

    public static function getContactsForKeyKeeperList($branch_id, $company_id = false){
        $where_company = '';
        if ($company_id) {
            $where_company = " AND mctc.company_id = '".$company_id."'";
        }
        $connection = Yii::$app->db;
        $contacts = $connection
                    ->createCommand("
                        SELECT mctc.contact_face_id, mctc.comment, co.name as office, cf.name as name
                        FROM manag_companies_to_contacts mctc
                        LEFT JOIN contact_faces cf ON mctc.contact_face_id = cf.id
                        LEFT JOIN contacts_offices co ON mctc.contact_office_id = co.id
                        WHERE mctc.branch_id = '{$branch_id}' AND mctc.publication_status = 1".$where_company
                    )
                    ->queryAll();

        foreach ($contacts as $key => $contact) {
            $contacts[$key]['name'] = $contact["name"].' ('.$contact["office"];
            if ($contact["comment"] != '') {
                $contacts[$key]['name'] .= ', '.$contact["comment"];
            }
            $contacts[$key]['name'] .= ')';
        }

        return ArrayHelper::map($contacts, 'contact_face_id', 'name');
    }
}
