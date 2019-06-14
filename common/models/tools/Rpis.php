<?php
namespace common\models\tools;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;

class Rpis extends ActiveRecord
{
    public static function tableName()
    {
        return 'rpis';
    }

    public function search($params){
        $query = self::find();
        $dataProvider = new ActiveDataProvider([
            'query'=>$query,
            'sort' => ['defaultOrder' => ['config' => SORT_ASC]],
            'pagination'=>[
                'pageSize' => 10,
                'pageSizeParam'=>false,
                'forcePageParam' => false
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        // adjust the query by adding the filters
        $query->andFilterWhere(['like','ip',$this->ip]);
        $query->andFilterWhere(['ilike','mac',$this->mac]);
        $query->andFilterWhere(['ilike','config',$this->config]);
//        $query->andFilterWhere(['username' => $this->username]);
//        $query->andFilterWhere(['ipv6_prefix' => $this->ipv6]);
//        $query->andFilterWhere(['>=', 'stopped_at', $this->begin]);//->orFilterWhere(['>=', 'stopped_at', $this->begin]);
//        $query->andFilterWhere(['<=', 'started_at', $this->end]);//->orFilterWhere(['<=', 'stopped_at', $this->end]);
        return $dataProvider;
    }
}