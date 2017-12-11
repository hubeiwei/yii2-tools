<?php

namespace hubeiwei\yii2tools\extensions;

use Yii;

trait QueryCacheTrait
{
    /**
     * 缓存时间，单位：秒，用0来表示永久缓存，用负数来表示不使用缓存
     * @var int
     * @see cache()
     */
    public $queryCacheDuration;
    /**
     * 缓存依赖
     * @var \yii\caching\Dependency
     * @see cache()
     */
    public $queryCacheDependency;


    /**
     * @param int $duration 缓存时间，单位：秒，用0来表示永久缓存，用负数来表示不使用缓存
     * @param \yii\caching\Dependency $dependency 缓存依赖
     * @return $this
     */
    public function cache($duration = null, $dependency = null)
    {
        $this->queryCacheDuration = $duration;
        $this->queryCacheDependency = $dependency;
        return $this;
    }

    /**
     * Creates a DB command that can be used to execute this query.
     * @param \yii\db\Connection $db the database connection used to generate the SQL statement.
     * If this parameter is not given, the `db` application component will be used.
     * @return \yii\db\Command the created DB command instance.
     */
    public function createCommand($db = null)
    {
        if ($db === null) {
            $db = Yii::$app->getDb();
        }
        $command = parent::createCommand($db);
        if ($this->queryCacheDuration !== null || $this->queryCacheDependency !== null) {
            $command->queryCacheDuration = $this->queryCacheDuration === null ? $db->queryCacheDuration : $this->queryCacheDuration;
            $command->queryCacheDependency = $this->queryCacheDependency;
        }
        return $command;
    }
}
