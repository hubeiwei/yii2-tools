<?php

namespace hubeiwei\yii2tools\extensions;

use Yii;
use yii\db\ActiveRecord as YiiActiveRecord;

class ActiveRecord extends YiiActiveRecord
{
    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * 清空表
     * @return int
     */
    public static function truncateTable()
    {
        return static::getDb()
            ->createCommand()
            ->truncateTable(static::tableName())
            ->execute();
    }

    /**
     * 获取第一条错误
     * @return string
     */
    public function getFirstErrorString()
    {
        if ($this->hasErrors()) {
            return current($this->getFirstErrors());
        }
        return '';
    }
}
