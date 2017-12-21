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
     * @see \yii\db\Query::createCommand()
     */
    public function createCommand($db = null)
    {
        if ($db === null) {
            $db = Yii::$app->getDb();
        }
        $command = parent::createCommand($db);
        $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        return $command;
    }
}
