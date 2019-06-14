<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\CasUser;

/**
 * CasUserSearch represents the model behind the search form about `common\models\CasUser`.
 */
class CasUserSearch extends CasUser
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cas_id', 'department_id'], 'integer'],
            [['login', 'roles', 'first_name', 'last_name', 'middle_name'], 'safe'],
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
        $query = CasUser::find();

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
            'cas_id' => $this->cas_id,
            'department_id' => $this->department_id,
        ]);

        $query->andFilterWhere(['ilike', 'login', $this->login])
            ->andFilterWhere(['like', 'roles', $this->roles])
            ->andFilterWhere(['ilike', 'first_name', $this->first_name])
            ->andFilterWhere(['ilike', 'last_name', $this->last_name])
            ->andFilterWhere(['ilike', 'middle_name', $this->middle_name]);

        return $dataProvider;
    }
}
