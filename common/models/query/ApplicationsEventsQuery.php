<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ApplicationsEvents]].
 *
 * @see ApplicationsEvents
 */
class ApplicationsEventsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApplicationsEvents[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApplicationsEvents|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
