<?php

namespace hubeiwei\yii2tools\grid;

use kartik\grid\DataColumn as KartikDataColumn;
use kartik\grid\GridView;

/**
 * 后台大量使用该类时，为了减少重复配置，应该扩展出来
 */
class DataColumn extends KartikDataColumn
{
    /**
     * 如果是用 css 控制 td 居中，那么 Select2 的文字也会被居中，在这里配置效果更好
     */
    public $hAlign = GridView::ALIGN_CENTER;

    /**
     * 垂直居中
     */
    public $vAlign = GridView::ALIGN_MIDDLE;

    /**
     * 默认不换行
     */
    public $noWrap = true;

    public $filterOptions = [
        'style' => [
            'min-width' => '80px',
        ],
    ];
}
