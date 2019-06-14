<?php

namespace common\models\history;

use Yii;

/**
 * This is the model class for table "zones__addresses_history".
 *
 * @property integer $id
 * @property integer $origin_id
 * @property string $address_uuid
 * @property integer $all_flats
 * @property integer $address_type_id
 * @property string $comment
 * @property integer $district_id
 * @property integer $area_id
 * @property integer $build_status_individual
 * @property integer $manag_company_id
 * @property integer $build_status_entity
 * @property string $coordinates
 * @property string $connection_cost_individual
 * @property string $connection_cost_entity
 * @property integer $all_offices
 * @property integer $manag_company_branch_id
 * @property integer $key_keeper
 * @property integer $cas_user_id
 * @property integer $created_at
 * @property integer $contract_with_manag_company
 * @property integer $publication_status
 */
class ZonesAddressesHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'zones__addresses_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['origin_id', 'address_uuid', 'build_status_individual', 'build_status_entity', 'cas_user_id', 'created_at', 'contract_with_manag_company', 'publication_status'], 'required'],
            [['origin_id', 'all_flats', 'address_type_id', 'district_id', 'area_id', 'build_status_individual', 'manag_company_id', 'build_status_entity', 'all_offices', 'manag_company_branch_id', 'key_keeper', 'cas_user_id', 'created_at', 'contract_with_manag_company', 'publication_status'], 'integer'],
            [['comment'], 'string'],
            [['address_uuid', 'coordinates', 'connection_cost_individual', 'connection_cost_entity'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'origin_id' => 'Origin ID',
            'address_uuid' => 'Address Uuid',
            'all_flats' => 'All Flats',
            'address_type_id' => 'Address Type ID',
            'comment' => 'Comment',
            'district_id' => 'District ID',
            'area_id' => 'Area ID',
            'build_status_individual' => 'Build Status Individual',
            'manag_company_id' => 'Manag Company ID',
            'build_status_entity' => 'Build Status Entity',
            'coordinates' => 'Coordinates',
            'connection_cost_individual' => 'Connection Cost Individual',
            'connection_cost_entity' => 'Connection Cost Entity',
            'all_offices' => 'All Offices',
            'manag_company_branch_id' => 'Manag Company Branch ID',
            'key_keeper' => 'Key Keeper',
            'cas_user_id' => 'Cas User ID',
            'created_at' => 'Created At',
            'contract_with_manag_company' => 'Contract With Manag Company',
            'publication_status' => 'Publication Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ZonesAddressesHistoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ZonesAddressesHistoryQuery(get_called_class());
    }
}
