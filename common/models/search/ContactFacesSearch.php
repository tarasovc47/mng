<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ContactFaces;

/**
 * ContactFacesSearch represents the model behind the search form about `common\models\ContactFaces`.
 */
class ContactFacesSearch extends ContactFaces
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'cas_user_id', 'publication_status', 'phones', 'manag_companies'], 'integer'],
            [['name', 'comment', 'emails'], 'safe'],
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
        $query = ContactFaces::find();

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

        $query->leftJoin('contact_faces_phones', 'contact_faces_phones.contact_face_id = contact_faces.id');
        $query->leftJoin('contact_faces_emails', 'contact_faces_emails.contact_face_id = contact_faces.id');
        $query->leftJoin('manag_companies_to_contacts', 'manag_companies_to_contacts.contact_face_id = contact_faces.id');

        // grid filtering conditions
        $query->andFilterWhere([
            'contact_faces.id' => $this->id,
            'contact_faces.created_at' => $this->created_at,
            'contact_faces.cas_user_id' => $this->cas_user_id,
            'contact_faces.publication_status' => $this->publication_status,
        ]);

        $query->andFilterWhere(['ilike', 'contact_faces.name', $this->name])
            ->andFilterWhere(['ilike', 'contact_faces.comment', $this->comment])
            ->andFilterWhere(['ilike', 'contact_faces_phones.phone', $this->phones])
            ->andFilterWhere(['ilike', 'contact_faces_emails.email', $this->emails])
            ->andFilterWhere(['in', 'manag_companies_to_contacts.company_id', $this->manag_companies]);

        $query->groupBy('contact_faces.id');

        return $dataProvider;
    }
}
