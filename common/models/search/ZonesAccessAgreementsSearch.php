<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ZonesAccessAgreements;

/**
 * ZonesAccessAgreementsSearch represents the model behind the search form about `common\models\ZonesAccessAgreements`.
 */
class ZonesAccessAgreementsSearch extends ZonesAccessAgreements
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'oper_id', 'manag_company_id', 'created_at', 'closed_at', 'opened_at', 'auto_prolongation'], 'integer'],
            [['name', 'label', 'comment'], 'safe'],
            [['rent_price'], 'number'],
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
        $query = ZonesAccessAgreements::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'oper_id' => $this->oper_id,
            'manag_company_id' => $this->manag_company_id,
            'created_at' => $this->created_at,
            'opened_at' => $this->opened_at,
            'closed_at' => $this->closed_at,
            'rent_price' => $this->rent_price,
            'auto_prolongation' => $this->auto_prolongation,
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'label', $this->label])
            ->andFilterWhere(['ilike', 'comment', $this->comment]);

        if ($this->opened_at) {
            $this->opened_at = $opened_at;
        }
        if ($this->closed_at) {
            $this->closed_at = $closed_at;
        }
        return $dataProvider;
    }
}
