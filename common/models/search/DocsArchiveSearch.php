<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\DocsArchive;

/**
 * DocsArchiveSearch represents the model behind the search form about `common\models\DocsArchive`.
 */
class DocsArchiveSearch extends DocsArchive
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'doc_type_id', 'abonent', 'cas_user_id', 'opened_at', 'parent_id', 'publication_status'], 'integer'],
            [['name', 'label', 'descr', 'client_id'], 'safe'],
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
        $query = DocsArchive::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if ($this->opened_at) {
            $opened_at = $this->opened_at;
            $this->opened_at = strtotime($this->opened_at);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'doc_type_id' => $this->doc_type_id,
            'opened_at' => $this->opened_at,
            'abonent' => $this->abonent,
            'cas_user_id' => $this->cas_user_id,
            'publication_status' => $this->publication_status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'descr', $this->descr])
            ->andFilterWhere(['like', 'client_id', $this->client_id]);

        if ($this->opened_at) {
            $this->opened_at = $opened_at;
        }

        return $dataProvider;
    }
}
