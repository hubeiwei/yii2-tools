<?php

namespace hubeiwei\yii2tools\extensions;

use yii\db\ActiveQuery as YiiActiveQuery;

class ActiveQuery extends YiiActiveQuery
{
    use QueryTrait;
    use QueryCacheTrait;

    /**
     * Creates a DB command that can be used to execute this query.
     * @param \yii\db\Connection|null $db the DB connection used to create the DB command.
     * If `null`, the DB connection returned by [[modelClass]] will be used.
     * @return \yii\db\Command the created DB command instance.
     */
    public function createCommand($db = null)
    {
        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        if ($db === null) {
            $db = $modelClass::getDb();
        }
        $command = parent::createCommand($db);
        if ($this->queryCacheDuration !== null || $this->queryCacheDependency !== null) {
            $command->queryCacheDuration = $this->queryCacheDuration === null ? $db->queryCacheDuration : $this->queryCacheDuration;
            $command->queryCacheDependency = $this->queryCacheDependency;
        }
        return $command;
    }
}
