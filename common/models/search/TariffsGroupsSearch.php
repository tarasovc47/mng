<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TariffsGroups;

class TariffsGroupsSearch extends TariffsGroups
{
    public function rules()
    {
        return [
            [['id', 'publication_status', 'created_at', 'cas_user_id', 'abonent_type'], 'integer'],
            [['name', 'comment'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TariffsGroups::find();

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
        $query->andFilterWhere([
            'id' => $this->id,
            'publication_status' => $this->publication_status,
            'created_at' => $this->created_at,
            'cas_user_id' => $this->cas_user_id,
            'abonent_type' => $this->abonent_type,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name]);
        $query->andFilterWhere(['ilike', 'comment', $this->comment]);

        return $dataProvider;
    }
}
