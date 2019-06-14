<?php
namespace common\models\modules_settings;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;
class MngModules extends ActiveRecord
{
    public static function tableName()
    {
        return 'modules';
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'descr' => 'Описание',
        ];
    }

    public function rules()
    {
        return [
            [['name', 'descr'], 'required'],
            [['name', 'descr'], 'string', 'max' => 255],
            [['name', 'descr'], 'trim'],
            [['name'], 'unique'],
        ];
    }

    public function search($params)
    {
        $query = MngModules::find();

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
        ]);

        $query->andFilterWhere(['ilike', 'name', $this->name])
            ->andFilterWhere(['ilike', 'cas_name', $this->descr]);

        return $dataProvider;
    }

}