<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[ZonesAccessAgreements]].
 *
 * @see ZonesAccessAgreements
 */
class ZonesAccessAgreementsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ZonesAccessAgreements[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ZonesAccessAgreements|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
