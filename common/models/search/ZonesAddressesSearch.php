<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ZonesAddresses;
use common\components\SiteHelper;

class ZonesAddressesSearch extends ZonesAddresses
{
    public function rules()
    {
        return [
            [['id', 'address_type_id', 'district_id', 'area_id', 'manag_company_id', 'opers'], 'integer'],
            [['comment', 'address_uuid'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = ZonesAddresses::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->leftJoin('zones__addresses_to_opers', 'zones__addresses_to_opers.address_id = zones__addresses.id');

        $query->andFilterWhere([
            'zones__addresses.id' => $this->id,
            'zones__addresses.address_type_id' => $this->address_type_id,
            'zones__addresses.district_id' => $this->district_id,
            'zones__addresses.area_id' => $this->area_id,
            'zones__addresses.manag_company_id' => $this->manag_company_id,
            'zones__addresses_to_opers.oper_id' => $this->opers,
        ]);

        $query->andFilterWhere(['ilike', 'comment', $this->comment]);

        if(!empty($this->address_uuid)){
            $this->address_uuid = SiteHelper::to_php_array($this->address_uuid);
            $query->andFilterWhere(['in', 'zones__addresses.address_uuid', $this->address_uuid]);
        }

        $query->groupBy('zones__addresses.id');

        return $dataProvider;
    }
}
