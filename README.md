# yii2-tools

自己在 yii2 上封装的一些东西，在公司也这么用，虽然不能满足所有人的口味，但如果你觉得好的话，你可以拿来用。以后我会花时间把这些东西做成比较通用可配置的。

## 安装

执行：

```
composer require hubeiwei/yii2-tools 1.0.x-dev
```

或者添加：

```
"hubeiwei/yii2-tools": "1.0.x-dev"
```

因为是我自己用的东西，灵活性不一定高，如果你觉得这些代码不能100%满足你，你需要进行一些改动的话，你可以直接把代码下载下来，在 composer.json 里添加：

```
"autoload": {
    "psr-4": {
        "hubeiwei\\yii2tools\\": "path/to/hubeiwei/yii2-tools"
    }
}
```

然后执行 `composer dump-autoload` 即可。

## 使用

除了以下我给的一些使用方法，你也可以去看看[我的项目](https://github.com/hubeiwei/hello-yii2)。

### 查询

首先，你的 model 需要继承 `hubeiwei\yii2tools\extensions\ActiveRecord`，或使用 `hubeiwei\yii2tools\extensions\Query`。

```php
$query = User::find();
// or
$query = (new \hubeiwei\yii2tools\extensions\Query());

$query->compare('money', 1)                                                    // WHERE money = 1
    ->compare('money', '>1,,<3 =2')                                            // WHERE money > 1 AMD money < 3 AND money = 2
    ->timeRangeFilter('time', '2017/01/01 - 2018/01/01', true)                 // WHERE time BETWEEM 1483200000 AND 1514822399
    ->timeRangeFilter('time', '2017/01/01 01:01:01 - 2018/01/01 23:59:59');    // WHERE time BETWEEM 1483203661 AND 1514822399
```

### Widget

你 model 的枚举字段可以这样写:

```php
const STATUS_ACTIVE = 1;
const STATUS_INACTIVE = 0;
public static $status_map = [
    self::STATUS_ACTIVE => '启用',
    self::STATUS_INACTIVE => '禁用',
];
```

view:

```php
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

    // 枚举字段过滤
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return User::$status_map[$model->status];
        },
        'filterType' => Select2::className(),
        'filterWidgetOptions' => [
            'data' => User::$status_map,
        ],
    ],

    // 时间范围过滤，查询参考上面的
    [
        'attribute' => 'created_at',
        'format' => 'dateTime',
        'filterType' => DateRangePicker::className(),
    ],

    ['class' => ActionColumn::className()],
];

// GridView
echo RenderHelper::gridView($dataProvider, $gridColumns, $searchModel);

// DynaGrid
echo RenderHelper::dynaGrid('grid-id', $dataProvider, $gridColumns, $searchModel);
```

### 消息提示

设置消息

```php
use hubeiwei\yii2tools\helpers\Message;

\Yii::$app->session->setFlash(Message::TYPE_INFO, 'some message');
// or
Message::setSuccessMsg('success message');
// or
Message::setErrorMsg(['error1 message', 'error2 message']);
```

输出消息:

```php
use hubeiwei\yii2tools\widgets\Alert;
use hubeiwei\yii2tools\widgets\Growl;

echo Alert::widget();
// or
echo Growl::widget();
```

### 在 view 如何更好的引入 js 和 css 到布局

来源：

* [https://getyii.com/topic/9](https://getyii.com/topic/9)

* [https://getyii.com/topic/10](https://getyii.com/topic/10)

```php
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
