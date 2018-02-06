<?php

namespace hubeiwei\yii2tools\extensions;

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
     * Disables query cache for this query.
     * @return $this the query object itself
     */
    public function noCache()
    {
        $this->queryCacheDuration = -1;
        return $this;
    }

    /**
     * @see Command::cache() this has default duration
     * @return bool
     */
    public function hasCache()
    {
        return $this->queryCacheDuration !== null || $this->queryCacheDependency !== null;
    }

    /**
     * @inheritdoc
     */
    protected function _queryScalar($selectExpression, $db)
    {
        if ($this->emulateExecution) {
            return null;
        }

        if (
            !$this->distinct
            && empty($this->groupBy)
            && empty($this->having)
            && empty($this->union)
        ) {
            $select = $this->select;
            $order = $this->orderBy;
            $limit = $this->limit;
            $offset = $this->offset;

            $this->select = [$selectExpression];
            $this->orderBy = null;
            $this->limit = null;
            $this->offset = null;
            $command = $this->createCommand($db);

            $this->select = $select;
            $this->orderBy = $order;
            $this->limit = $limit;
            $this->offset = $offset;

            return $command->queryScalar();
        }

        $command = (new Query())
            ->select([$selectExpression])
            ->from(['c' => $this])
            ->createCommand($db);
        if ($this->hasCache()) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }
        return $command->queryScalar();
    }
}
