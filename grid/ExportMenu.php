<?php

namespace hubeiwei\yii2tools\grid;

use kartik\export\ExportMenu as KartikExportMenu;

/**
 * 后台大量使用该类时，为了减少重复配置，应该扩展出来
 *
 * @see \hubeiwei\yii2tools\helpers\Render::gridView()
 * @see \hubeiwei\yii2tools\helpers\Render::dynaGrid()
 */
class ExportMenu extends KartikExportMenu
{
    public $exportFormView = '@vendor/kartik-v/yii2-export/views/_form';
    public $exportColumnsView = '@vendor/kartik-v/yii2-export/views/_columns';
    public $afterSaveView = '@vendor/kartik-v/yii2-export/views/_view';
}
