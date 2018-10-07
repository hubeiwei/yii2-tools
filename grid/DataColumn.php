<?php

namespace hubeiwei\yii2tools\grid;

use kartik\grid\DataColumn as KartikDataColumn;
use kartik\grid\GridView;

class DataColumn extends KartikDataColumn
{
    /**
     * 如果是用 css 控制 td 居中，那么 Select2 的文字也会被居中，在这里配置效果更好
     */
    public $hAlign = GridView::ALIGN_CENTER;

    public $vAlign = GridView::ALIGN_MIDDLE;

    public $noWrap = true;

    public $filterOptions = [
        'style' => [
            'min-width' => '80px',
        ],
    ];
}
