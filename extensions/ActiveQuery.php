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
        $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        return $command;
    }
}
