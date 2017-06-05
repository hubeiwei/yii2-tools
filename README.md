# yii2-tools

自己在 yii2 上封装的一些东西，在公司也这么用，虽然不能满足所有人的口味，写的代码也不认为很出彩，但如果你觉得好的话，你可以拿来用。以后我有空会花时间把这些东西做成比较通用可配置的。

## 安装

执行：

```
composer require hubeiwei/yii2-tools 1.0.x-dev
```

或者添加：

```
"hubeiwei/yii2-tools": "1.0.x-dev"
```

因为是我自己用的东西，灵活性不一定高，如果你觉得这些代码不能100%满足你，你需要进行一些改动的话，你可以直接把代码下载下来，添加：

```
"autoload": {
    "psr-4": {
        "hubeiwei\\yii2tools\\": "path/to/hubeiwei/yii2-tools"
    }
}
```

然后把我 composer.json 文件里 require 的包都加到你自己的 composer.json 里，执行 `composer update`。如果你已经有了这些包，直接执行 `composer dump-autoload` 即可。

## 使用

除了以下我给的一些使用方法，你也可以去看看[我的 DEMO](https://github.com/hubeiwei/hello-yii2)。

### 查询

首先，你的 model 需要继承 `hubeiwei\yii2tools\extensions\ActiveRecord`，或使用 `hubeiwei\yii2tools\extensions\Query`。

实例化 `ActiveQuery` 或 `Query`：

```
$query = \common\models\User::find();
// or
$query = (new \hubeiwei\yii2tools\extensions\Query());
```

数字对比：

```
// WHERE money = 1
$query->compare('money', 1);

// WHERE money > 1 AND money < 3 AND money = 2
$query->compare('money', '>1,,<3 =2');
```

日期范围过滤：

```
$dateRange = '2017/01/01 - 2018/01/01';

// WHERE time BETWEEM 1483200000 AND 1514822399
$query->timeRangeFilter('time', $dateRange, true);

// WHERE time BETWEEM 2017/01/01 AND 2018/01/01
$query->timeRangeFilter('time', $dateRange, true, false);
```

时间范围过滤：

```
$dateTimeRange = '2017/01/01 01:01:01 - 2018/01/01 23:59:59';

// WHERE time BETWEEM 1483203661 AND 1514822399
$query->timeRangeFilter('time', $dateTimeRange);

// WHERE time BETWEEM 2017/01/01 01:01:01 AND 2018/01/01 23:59:59
$query->timeRangeFilter('time', $dateTimeRange, false, false);
```

### Widget

你 model 的枚举字段可以这样写:

```
use yii\helpers\ArrayHelper;

const STATUS_ACTIVE = 1;
const STATUS_INACTIVE = 0;

/**
 * @param int $value
 * @return array|string|null
 */
public static function statusMap($value = -1)
{
    $map = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_INACTIVE => '禁用',
    ];
    if ($value == -1) {
        return $map;
    }
    return ArrayHelper::getValue($map, $value);
}
```

view:

```
use common\models\User;
use hubeiwei\yii2tools\grid\ActionColumn;
use hubeiwei\yii2tools\grid\SerialColumn;
use hubeiwei\yii2tools\helpers\RenderHelper;
use hubeiwei\yii2tools\widgets\DateRangePicker;
use hubeiwei\yii2tools\widgets\Select2;

/**
 * @var $this yii\web\View
 * @var $searchModel app\models\search\ArticleSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$gridColumns = [
    ['class' => SerialColumn::className()],

    // 枚举字段过滤（Html::dropDownList）
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return User::statusMap($model->status);
        },
        'filter' => RenderHelper::dropDownFilter($searchModel, 'status', User::statusMap()),
    ],

    // 枚举字段过滤（Select2）
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return User::statusMap($model->status);
        },
        'filterType' => Select2::className(),
        'filterWidgetOptions' => [
            'data' => User::statusMap(),
        ],
    ],

    // 时间范围过滤，查询的代码参考上面的 timeRangeFilter
    [
        'attribute' => 'created_at',
        'format' => 'dateTime',
        'filterType' => DateRangePicker::className(),
        // 'filterWidgetOptions' => ['dateOnly' => true],
    ],

    ['class' => ActionColumn::className()],
];

// GridView
echo RenderHelper::gridView($dataProvider, $gridColumns, $searchModel);

// DynaGrid
echo RenderHelper::dynaGrid('grid-id', $dataProvider, $gridColumns, $searchModel);
```

本节示例插件的 DEMO：

* [Select2](http://demos.krajee.com/widget-details/select2)
* [DateRangePicker](http://demos.krajee.com/date-range)
* [GridView](http://demos.krajee.com/grid-demo)
* [DynaGrid](http://demos.krajee.com/dynagrid-demo)
* [Export](http://demos.krajee.com/export-demo)：使用 `RenderHelper::gridView()` 时需要把 $hasExport 参数设置为 true 才能使用导出，而 `RenderHelper::dynaGrid()` 我让它直接使用了

### 消息提示

设置消息：

```
use hubeiwei\yii2tools\helpers\Message;

\Yii::$app->session->setFlash(Message::TYPE_INFO, 'some message');
// or
Message::setSuccessMsg('success message');
// or
Message::setErrorMsg(['error1 message', 'error2 message']);
```

输出消息：

```
use hubeiwei\yii2tools\widgets\Alert;
use hubeiwei\yii2tools\widgets\Growl;

echo Alert::widget();
// or
echo Growl::widget();
```

> 建议放在布局里，一劳永逸

本节示例插件的 DEMO：

* [Alert](http://v3.bootcss.com/components/#alerts)：这个没啥好说的，就是 bootstrap 的 Alert
* [Growl](http://demos.krajee.com/widget-details/growl)：进去之后看到一个表单，提交后可以看到 Demo

### 在 view 如何更好的把 js 和 css 注入到布局

来源：

* [Yii2 如何更好的在页面注入 JavaScript](https://getyii.com/topic/9)

* [Yii2 如何更好的在页面注入 CSS](https://getyii.com/topic/10)

```
<?php

use hubeiwei\yii2tools\widgets\CssBlock;
use hubeiwei\yii2tools\widgets\JsBlock;
use yii\web\View;

/**
 * @var $this yii\web\View
 */
?>

<?php CssBlock::begin(); ?>
<style>
    .my-width {
        width: 100%;
    }
</style>
<?php CssBlock::end(); ?>

<?php JsBlock::begin(['pos' => View::POS_END]); ?>
<script>
    function test() {
        console.log("test");
    }
</script>
<?php JsBlock::end(); ?>
```

> 原文：为什么要这么写？这样写的好处有两个，有代码提示和代码高亮。

## 打赏

如果觉得我做的东西对你有帮助的话，求打赏一杯 coffee，这样我会有更多动力去分享更多 yii2 的内容。

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/ali_pay.jpg" width="500px" alt="支付宝">

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/wechat_pay.png" width="500px" alt="微信">
