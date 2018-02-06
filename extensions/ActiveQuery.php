<?php

namespace hubeiwei\yii2tools\extensions;

use yii\db\ActiveQuery as YiiActiveQuery;

class ActiveQuery extends YiiActiveQuery
{
    use QueryTrait;
    use QueryCacheTrait;


    /**
     * @inheritdoc
     */
    public function createCommand($db = null)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        if ($db === null) {
            $db = $modelClass::getDb();
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
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        if ($db === null) {
            $db = $modelClass::getDb();
        }

        if ($this->sql === null) {
            return $this->_queryScalar($selectExpression, $db);
        }

        $command = (new Query())->select([$selectExpression])
            ->from(['c' => "({$this->sql})"])
            ->params($this->params)
            ->createCommand($db);
        if ($this->hasCache()) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }
        return $command->queryScalar();
    }
}
