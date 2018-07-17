<?php

namespace hubeiwei\yii2tools\grid;

use kartik\grid\GridView as KartikGridView;

/**
 * 这个类只是为了方便本人在使用时减少配置的内容
 *
 * 这里根据个人使用习惯封装了一段
 * @see \hubeiwei\yii2tools\helpers\Render::gridView()
 */
class GridView extends KartikGridView
{
    public $dataColumnClass = '\hubeiwei\yii2tools\grid\DataColumn';
    public $resizableColumns = false;
    public $bordered = true;
    public $striped = true;
    public $condensed = false;
    public $responsive = true;
    public $responsiveWrap = false;
    public $hover = true;
    public $export = false;
}
