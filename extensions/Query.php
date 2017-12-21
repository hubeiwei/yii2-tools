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
        if ($this->queryCacheDuration !== null || $this->queryCacheDependency !== null) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }
        return $command;
    }

    /**
     * @inheritdoc
     */
    protected function queryScalar($selectExpression, $db)
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

        $command = (new self())
            ->select([$selectExpression])
            ->from(['c' => $this])
            ->createCommand($db);
        if ($this->queryCacheDuration !== null || $this->queryCacheDependency !== null) {
            $command->cache($this->queryCacheDuration, $this->queryCacheDependency);
        }
        return $command->queryScalar();
    }
}
