<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ManagCompanies;

/**
 * ManagCompanySearch represents the model behind the search form about `common\models\ManagCompany`.
 */
class ManagCompaniesSearch extends ManagCompanies
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'publication_status', 'created_at', 'cas_user_id', 'company_type'], 'integer'],
            [['name', 'coordinates', 'comment'], 'safe'],
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
        $query = ManagCompanies::find();

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
            'company_type' => $this->company_type,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'coordinates', $this->coordinates])
            ->andFilterWhere(['ilike', 'comment', $this->comment]);

        return $dataProvider;
    }
}
