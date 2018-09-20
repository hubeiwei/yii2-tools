<?php

namespace hubeiwei\yii2tools\helpers;

use hubeiwei\yii2tools\grid\ExportMenu;
use hubeiwei\yii2tools\grid\GridView;
use kartik\dynagrid\DynaGrid;
use liyunfang\pager\LinkPager;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class Render
{
    /**
     * GridView
     * 这里配置好了大量的默认设置，基本上直接调用就可以了，手机端也能适应
     *
     * @param array $config kartik\grid\GridView 类的属性
     * 有部分特殊的参数如下：
     *
     * - `resetBtn`: 刷新按钮，默认为 true，也可以自定义 html 的内容，或者用 false 禁用。
     * - `export`: kartik\export\ExportMenu 的属性，使用 true 启用，或使用数组自定义配置，不传该参数或者传 false 禁用
     *
     * @return string
     */
    public static function gridView($config = [])
    {
        /** @var \yii\data\DataProviderInterface $dataProvider */
        $dataProvider = ArrayHelper::getValue($config, 'dataProvider');
        if (!($dataProvider instanceof DataProviderInterface)) {
            throw new InvalidConfigException('The "dataProvider" param must implement DataProviderInterface.');
        }

        $filterModel = ArrayHelper::getValue($config, 'filterModel');
        if (!empty($filterModel) && !($filterModel instanceof Model)) {
            throw new InvalidConfigException('The "filterModel" param must be instance of yii\base\Model');
        }

        $columns = ArrayHelper::getValue($config, 'columns');
        if (!is_array($columns) || empty($columns)) {
            throw new InvalidConfigException('The "columns" param must be a not null array');
        }

        $gridDefaultConfig = [
            'dataColumnClass' => 'hubeiwei\yii2tools\grid\DataColumn',
            'layout' => "{toolbar}\n{summary}\n{items}\n{pager}",
            'resizableColumns' => false,
            'bordered' => true,
            'striped' => true,
            'condensed' => false,
            'responsive' => true,
            'responsiveWrap' => false,
            'hover' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'id' => 'kartik-grid-pjax',
                    'scrollTo' => true,
                ],
            ],
            'filterSelector' => "input[name='" . $dataProvider->getPagination()->pageParam . "']",
            'pager' => [
                'class' => LinkPager::className(),
                'template' => '<div class="form-inline" style="padding: 10px 0 0;">{pageButtons}{customPage}</div>',
                'options' => [
                    'class' => ['pagination'],
                    'style' => [
                        'margin' => 0,
                        'float' => 'left',
                    ],
                ],
                'firstPageLabel' => '首页',
                'lastPageLabel' => '末页',
            ],
        ];

        $toolbar = [
            '{toggleData}',
        ];

        $resetBtn = ArrayHelper::remove($config, 'resetBtn', true);
        if ($resetBtn === true) {
            $resetBtn = Html::a(
                '<i class="glyphicon glyphicon-repeat"></i> 重置',
                [Yii::$app->controller->action->id],
                [
                    'class' => ['btn', 'btn-default'],
                    'title' => '重置搜索条件',
                    'data' => ['pjax' => 'true'],
                ]
            );
        }
        if ($resetBtn) {
            array_unshift($toolbar, $resetBtn);
        }

        $exportConfig = ArrayHelper::remove($config, 'export');
        if ($exportConfig) {
            $exportDefaultConfig = [
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
                'pjaxContainerId' => 'kartik-grid-pjax',
            ];
            if (is_array($exportConfig) && !empty($exportConfig)) {
                $exportConfig = ArrayHelper::merge($exportDefaultConfig, $exportConfig);
            } else {
                $exportConfig = $exportDefaultConfig;
            }
            $exportMenu = ExportMenu::widget($exportConfig);
            array_unshift($toolbar, $exportMenu);
        }

        $gridConfig = ArrayHelper::merge($gridDefaultConfig, $config);
        $gridConfig['toolbar'] = $toolbar;

        return GridView::widget($gridConfig);
    }

    /**
     * DynaGrid
     * 这里配置好了大量的默认设置，基本上直接调用就可以了，手机端也能适应
     *
     * @param array $config kartik\grid\GridView 类的属性
     * 有部分特殊的参数如下：
     *
     * - `resetBtn`: 刷新按钮，默认为 true，也可以自定义 html 的内容，或者用 false 禁用。
     * - `export`: kartik\export\ExportMenu 的属性，使用 true 启用，或使用数组自定义配置，不传该参数或者传 false 禁用
     *
     * @return string
     */
    public static function dynaGrid($config = [])
    {
        $id = ArrayHelper::remove($config, 'id');
        if (empty($id)) {
            $route = Yii::$app->controller->getRoute();
            $id = str_replace('/', '-', $route);
        }

        /** @var \yii\data\DataProviderInterface $dataProvider */
        $dataProvider = ArrayHelper::remove($config, 'dataProvider');
        if (!$dataProvider instanceof DataProviderInterface) {
            throw new InvalidConfigException('The "dataProvider" object must implement DataProviderInterface.');
        }

        $filterModel = ArrayHelper::remove($config, 'filterModel');
        if (!$filterModel instanceof Model) {
            throw new InvalidConfigException('The "filterModel" object must be instance of yii\base\Model');
        }

        $columns = ArrayHelper::getValue($config, 'columns');
        if (!is_array($columns) || empty($columns)) {
            throw new InvalidConfigException('The "columns" param must be a not null array');
        }

        $gridDefaultConfig = [
            'allowThemeSetting' => false,
            'gridOptions' => [
                'dataProvider' => $dataProvider,
                'filterModel' => $filterModel,
                'dataColumnClass' => 'hubeiwei\yii2tools\grid\DataColumn',
                'resizableColumns' => false,
                'bordered' => true,
                'striped' => true,
                'condensed' => false,
                'responsive' => true,
                'responsiveWrap' => false,
                'hover' => true,
                'export' => false,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'scrollTo' => true,
                    ],
                ],
                'filterSelector' => "input[name='" . $dataProvider->getPagination()->pageParam . "']",
                'pager' => [
                    'class' => LinkPager::className(),
                    'template' => '<div class="form-inline">{pageButtons}{customPage}</div>',
                    'firstPageLabel' => '首页',
                    'lastPageLabel' => '末页',
                ],
            ],
        ];

        $toolbar = [
            '{toggleData}',
            ['content' => '{dynagrid}{dynagridFilter}{dynagridSort}'],
        ];

        $resetBtn = ArrayHelper::remove($config, 'resetBtn', true);
        if ($resetBtn === true) {
            $resetBtn = Html::a(
                '<i class="glyphicon glyphicon-repeat"></i> 重置',
                [Yii::$app->controller->action->id],
                [
                    'class' => ['btn', 'btn-default'],
                    'title' => '重置搜索条件',
                    'data' => ['pjax' => 'true'],
                ]
            );
        }
        if ($resetBtn) {
            array_unshift($toolbar, $resetBtn);
        }

        $exportConfig = ArrayHelper::remove($config, 'export');
        if ($exportConfig) {
            $exportDefaultConfig = [
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'exportConfig' => [
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_EXCEL => false,
                ],
            ];
            if (is_array($exportConfig) && !empty($exportConfig)) {
                $exportConfig = ArrayHelper::merge($exportDefaultConfig, $exportConfig);
            } else {
                $exportConfig = $exportDefaultConfig;
            }
            $exportConfig['pjaxContainerId'] = $id . '-pjax';
            $exportMenu = ExportMenu::widget($exportConfig);
            array_unshift($toolbar, $exportMenu);
        }

        $gridConfig = ArrayHelper::merge($gridDefaultConfig, $config);
        $gridConfig['options']['id'] = $id;
        $gridConfig['gridOptions']['toolbar'] = $toolbar;

        return DynaGrid::widget($gridConfig);
    }
}
