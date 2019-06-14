<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Tariffs;

class TariffsSearch extends Tariffs
{
    public $opers;
    public $services;

    public function rules()
    {
        return [
            [['id', 'for_abonent_type', 'closed_at', 'package', 'opened_at', 'priority', 'price', 'public', 'speed', 'channels'], 'integer'],
            [['name', 'comment'], 'safe'],
            [['opers', 'services', 'billing_id', 'connection_technologies'], 'each', 'rule' => ['integer']],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Tariffs::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($this->opened_at) {
            $opened_at = $this->opened_at;
            $this->opened_at = strtotime($this->opened_at);
        }
        if ($this->closed_at) {
            $closed_at = $this->closed_at;
            $this->closed_at = strtotime($this->closed_at);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->leftJoin('tariffs_to_opers', 'tariffs_to_opers.tariff_id = tariffs.id');
        $query->leftJoin('tariffs_to_services', 'tariffs_to_services.tariff_id = tariffs.id');
        $query->leftJoin('tariffs_to_connection_technologies', 'tariffs_to_connection_technologies.tariff_id = tariffs.id');

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'for_abonent_type' => $this->for_abonent_type,
            'opened_at' => $this->opened_at,
            'closed_at' => $this->closed_at,
            'package' => $this->package,
            'priority' => $this->priority,
            'price' => $this->price,
            'speed' => $this->speed,
            'channels' => $this->channels,
            'public' => $this->public,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'comment', $this->comment])
            ->andFilterWhere(['in', 'tariffs_to_opers.oper_id', $this->opers])
            ->andFilterWhere(['in', 'tariffs_to_services.service_id', $this->services])
            ->andFilterWhere(['in', 'tariffs_to_connection_technologies.connection_technology_id', $this->connection_technologies]);

        $query->groupBy('tariffs.id');

        if ($this->opened_at) {
            $this->opened_at = $opened_at;
        }
        if ($this->closed_at) {
            $this->closed_at = $closed_at;
        }

        return $dataProvider;
    }


}
