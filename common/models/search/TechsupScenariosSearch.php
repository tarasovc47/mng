<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TechsupScenarios;

class TechsupScenariosSearch extends TechsupScenarios
{
    public function rules()
    {
        return [
            [['department_id', 'status'], 'integer'],
            [['techsup_attribute_id'], 'string'],
            [['name', 'descr'], 'safe'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = TechsupScenarios::find();

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

        $query->leftJoin('attributes', 'attributes.id = techsup__scenarios.techsup_attribute_id');

        // grid filtering conditions
        $query->andFilterWhere([
            // 'techsup_attribute_id' => $this->techsup_attribute_id,
            'department_id' => $this->department_id,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'attributes.name', $this->techsup_attribute_id])
            ->andFilterWhere(['ilike', 'descr', $this->descr]);

        return $dataProvider;
    }
}
