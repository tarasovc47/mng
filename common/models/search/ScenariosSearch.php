<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Scenarios;

/**
 * ScenariosSearch represents the model behind the search form about `common\models\Scenarios`.
 */
class ScenariosSearch extends Scenarios
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'department_id_start', 'department_id_end', 'status'], 'integer'],
            [['condition_attrs', 'name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Scenarios::find();

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
            'department_id_start' => $this->department_id_start,
            'department_id_end' => $this->department_id_end,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'condition_attrs', $this->condition_attrs])
            ->andFilterWhere(['ilike', 'name', $this->name]);

        return $dataProvider;
    }
}
