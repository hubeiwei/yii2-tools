<?php

namespace hubeiwei\yii2tools\grid;

use kartik\grid\ActionColumn as KartikActionColumn;

/**
 * 后台大量使用该类时，为了减少重复配置，应该扩展出来
 */
class ActionColumn extends KartikActionColumn
{
    /**
     * 默认不换行
     */
    public $noWrap = true;
}
