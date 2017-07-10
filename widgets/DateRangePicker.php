<?php

namespace hubeiwei\yii2tools\widgets;

use kartik\daterange\DateRangePicker as KartikDateRangePicker;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

class DateRangePicker extends KartikDateRangePicker
{
    public $convertFormat = true;
    public $presetDropdown = true;
    /**
     * @var bool 根据该属性决定默认配置
     */
    public $dateOnly = false;
    /**
     * @var string 日期格式，如不配置则会根据 $dateOnly 来分配'Y/m/d'或'Y/m/d H:i:s'
     */
    public $dateFormat;
    /**
     * @var string 两个日期之间的分隔符
     */
    public $separator = ' - ';

    /**
     * @inheritdoc
     */
    protected function initSettings()
    {
        $this->pluginOptions = ArrayHelper::merge(
            $this->defaultPluginSettings(),
            $this->pluginOptions
        );
        parent::initSettings();
    }

    /**
     * 这个类的作用就是为了减少调用时的配置，所以我会写死一些默认的配置
     *
     * @return array
     */
    protected function defaultPluginSettings()
    {
        $format = $this->dateFormat;
        $pluginOptions = [
            'showDropdowns' => true,
            'opens' => 'left',
            'locale' => [
                'separator' => $this->separator,
            ],
        ];
        if ($this->dateOnly === false) {
            if($format == null){
                $format = 'Y/m/d H:i:s';
            }
            $pluginOptions = ArrayHelper::merge(
                $pluginOptions,
                [
                    'timePicker' => true,
                    'timePicker24Hour' => true,
                    'timePickerIncrement' => 1,
                    'timePickerSeconds' => true,
                    'locale' => [
                        'format' => $format,
                    ],
                ]
            );
        } else {
            if($format == null){
                $format = 'Y/m/d';
            }
            $pluginOptions = ArrayHelper::merge(
                $pluginOptions,
                [
                    'locale' => [
                        'format' => $format,
                    ],
                ]
            );
        }
        return $pluginOptions;
    }

    /**
     * @inheritdoc
     */
    protected function initLocale()
    {
        // 重写该方法只是为了在第一行代码处指定资源路径，其他不变
        $this->setLanguage('', Yii::getAlias('@kartik/daterange/assets'));
        if (empty($this->_langFile)) {
            return;
        }
        $localeSettings = ArrayHelper::getValue($this->pluginOptions, 'locale', []);
        $localeSettings += [
            'applyLabel' => Yii::t('kvdrp', 'Apply'),
            'cancelLabel' => Yii::t('kvdrp', 'Cancel'),
            'fromLabel' => Yii::t('kvdrp', 'From'),
            'toLabel' => Yii::t('kvdrp', 'To'),
            'weekLabel' => Yii::t('kvdrp', 'W'),
            'customRangeLabel' => Yii::t('kvdrp', 'Custom Range'),
            'daysOfWeek' => new JsExpression('moment.weekdaysMin()'),
            'monthNames' => new JsExpression('moment.monthsShort()'),
            'firstDay' => new JsExpression('moment.localeData()._week.dow')
        ];
        $this->pluginOptions['locale'] = $localeSettings;
    }
}
