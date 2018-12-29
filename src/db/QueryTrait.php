<?php

namespace hubeiwei\yii2tools\db;

use hubeiwei\yii2tools\helpers\Helper;

trait QueryTrait
{
    /**
     * @var string 时间分隔符
     */
    public $timeRangeSeparator = '-';


    /**
     * @param string $attribute
     *
     * @param string $expression
     * 格式可以是 "1,<2,!=3" 或 "1 <2 !=3"
     * @see Helper::unifyLimiter() 你看这个方法就明白了
     *
     * @return $this
     */
    public function compare($attribute, $expression)
    {
        $value = '';
        $expression = "$expression";
        $conditions = explode(',', Helper::unifyLimiter($expression));
        foreach ($conditions as $condition) {
            if (preg_match('/^(?:\s*(<>|!=|<=|>=|<|>|=))?(.*)$/', $condition, $matches)) {
                $op = $matches[1];
                $value = $matches[2];
            } else {
                $op = '';
            }

            if ($value === '') {
                continue;
                // return $this;
            }

            if ($op === '') {
                $op = '=';
            }

            $this->andFilterWhere([$op, $attribute, $value]);
        }

        return $this;
    }

    /**
     * @see \hubeiwei\yii2tools\widgets\DateRangePicker
     *
     * @param string $attribute
     * @param string $value
     * @param bool $dateOnly 输入的格式是否仅为日期（不包含时间），默认为 false
     * @param bool $formatDate 日期格式化成时间戳，默认为 true
     * @return $this
     */
    public function timeRangeFilter($attribute, $value, $dateOnly = false, $formatDate = true)
    {
        if ($value != '') {
            $value = "$value";
            $conditions = explode($this->timeRangeSeparator, $value);
            if (count($conditions) != 2) {
                return $this;
            }

            $from = $formatDate ? strtotime(trim($conditions[0])) : trim($conditions[0]) . ' 00:00:00';
            if ($from == false) {
                return $this;
            }

            if ($dateOnly) {
                $to = $formatDate ? strtotime(trim($conditions[1])) + 24 * 60 * 60 - 1 : trim($conditions[1]) . ' 23:59:59';
            } else {
                $to = $formatDate ? strtotime(trim($conditions[1])) : trim($conditions[1]);
            }
            if ($from == false) {
                return $this;
            }

            $this->andFilterWhere(['between', $attribute, $from, $to]);
        }
        return $this;
    }
}
