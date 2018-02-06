<?php

namespace hubeiwei\yii2tools\extensions;

use Yii;
use yii\db\Query as YiiQuery;

class Query extends YiiQuery
{
    use QueryTrait;
    use QueryCacheTrait;


    /**
     * @inheritdoc
     */
    public function createCommand($db = null)
    {
        if ($db === null) {
            $db = Yii::$app->getDb();
        }
        $command = parent::createCommand($db);
        if ($this->hasCache()) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }
        return $command;
    }

    /**
     * @inheritdoc
     */
    protected function queryScalar($selectExpression, $db)
    {
        return $this->_queryScalar($selectExpression, $db);
    }
}
