<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fields;

class FieldsSearch extends Fields
{
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['label', 'name', 'descr', 'data'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Fields::find();

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
            'id' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'label', $this->label])
            ->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'descr', $this->descr])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
