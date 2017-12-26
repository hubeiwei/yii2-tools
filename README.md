# yii2-tools

自己在 yii2 上根据使用习惯来封装的一些东西，具体功能直接往下看吧。以后我有空会花时间把这些东西做成更灵活的可配置的。

除了以下我给的一些使用方法，你也可以去看看[我的 DEMO](https://github.com/hubeiwei/hello-yii2)。

> 语文不好，你看到这句话说明我已经大改了一次。

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

## model

`ActiveQuery` 和 `Query` 类通过 `hubeiwei\yii2tools\extensions\ActiveQuery\QueryTrait` 来获得数字范围过滤以及日期范围过滤的功能。

使用方法有以下3种：

1:如果你 model 继承的类还是 `yii\db\ActiveRecord`，你可以改成 `hubeiwei\yii2tools\extensions\ActiveRecord`。

2:如果你已经有了自己的 `ActiveRecord` 类，但并没有 `ActiveQuery` 类，你可以重写 `find()` 方法改成类似如下的代码：

```php
public static function find()
{
    return Yii::createObject('hubeiwei\yii2tools\extensions\ActiveQuery', [
        get_called_class(),
        // 如果你想把下面的配置在这段代码里分离，就继续往下看。
        [
            'timeRangeSeparator' => '~',
        ],
    ]);
}
```

3:如果你已经有了自己的 `ActiveQuery` 和 `Query` 类，你可以直接引入我的 trait：`\hubeiwei\yii2tools\extensions\ActiveQuery\QueryTrait`。

### 配置

日期范围过滤的默认分割字符串是“-”，如果你想修改这个配置，方法如下：

1:常规方法，在 `ActiveRecord::find()` 方法里实例化后重新赋值后返回（就是使用方法里的第2条），或者在调用这个方法后重新赋值。

2:DI 容器，在你的 bootstrap.php 文件添加以下代码：

```php
// 因为存在上面的第3种使用方法，这里配置的类需要根据你具体用到的 `ActiveQuery` 类而定。
Yii::$container->set('hubeiwei\yii2tools\extensions\ActiveQuery', [
    'timeRangeSeparator' => '~',
]);
```

> basic 模板没有 bootstrap.php 文件，可以参考 advanced 模板。

`Query` 类可以在实例化之后修改，也可以继续往下看其他修改方法。

### 查询

实例化 `ActiveQuery` 或 `Query`：

```php
$query = \common\models\User::find();
// or
$query = new \hubeiwei\yii2tools\extensions\Query([
    'timeRangeSeparator' => '~',
]);
```

缓存：

```
$query->cache(7200)->all();
```

数字范围过滤：

```php
// WHERE money = 1
$query->compare('money', 1);

// WHERE money > 1 AND money < 3 AND money = 2
$query->compare('money', '>1,,<3 =2');
```

日期范围过滤：

```php
$dateRange = '2017/01/01 - 2018/01/01';

// WHERE time BETWEEM 1483200000 AND 1514822399
$query->timeRangeFilter('time', $dateRange, true);

// WHERE time BETWEEM '2017/01/01 00:00:00' AND '2018/01/01 23:59:59'
$query->timeRangeFilter('time', $dateRange, true, false);
```

时间范围过滤：

```php
$dateTimeRange = '2017/01/01 01:01:01 - 2018/01/01 23:59:59';

// WHERE time BETWEEM 1483203661 AND 1514822399
$query->timeRangeFilter('time', $dateTimeRange);

// WHERE time BETWEEM '2017/01/01 01:01:01' AND '2018/01/01 23:59:59'
$query->timeRangeFilter('time', $dateTimeRange, false, false);
```

附：`Query` 类还有更多的实例化方法，例如通过 DI 容器来配置，并可以通过注释来提供代码提示：

```php
use hubeiwei\yii2tools\extensions\Query;

/** @var $query Query */

// 设置
Yii::$container->set(Query::className(), [
    'timeRangeSeparator' => '~',
]);
// 实例化
$query = Yii::createObject(Query::className());
$query = Yii::$container->get(Query::className());

// 设置
Yii::$container->set('query', [
    'class' => 'hubeiwei\yii2tools\extensions\Query',
    'timeRangeSeparator' => '~',
]);
// 或
Yii::$container->set('query', function () {
    return new \hubeiwei\yii2tools\extensions\Query([
        'timeRangeSeparator' => '~',
    ]);
});
// 实例化
$query = Yii::$container->get('query');
```

> 设置这一步是在 bootstrap.php 文件进行的。

## widget

下面代码是枚举字段查询用到的，仅仅是提供一种方便维护的参考，如果你有自己的解决方案可以跳过。

```php
use yii\helpers\ArrayHelper;

const STATUS_ACTIVE = 1;
const STATUS_INACTIVE = 0;

/**
 * @param int $value
 * @return array|string|null
 */
public static function statusMap($value = null)
{
    $map = [
        self::STATUS_ACTIVE => '启用',
        self::STATUS_INACTIVE => '禁用',
    ];
    if ($value == null) {
        return $map;
    }
    return ArrayHelper::getValue($map, $value);
}
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

    // 枚举字段过滤（Html::dropDownList()）
    [
        'attribute' => 'status',
        'value' => function ($model) {
            return User::statusMap($model->status);
        },
        'filter' => RenderHelper::dropDownFilter($searchModel, 'status', User::statusMap()),
        // 当然你在 filter 这里直接给 User::statusMap() 也可以，你对比一下就发现两种方法的区别了
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

    // 时间范围过滤，查询的代码已经在上面有给出
    [
        'attribute' => 'created_at',
        'format' => 'dateTime',
        'filterType' => DateRangePicker::className(),
        /*'filterWidgetOptions' => [
            'dateOnly' => true,
            'dateFormat' => 'Y/m/d',
            'separator' => ' ~ ',
        ],*/
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

## 消息提示

设置消息：

```php
use hubeiwei\yii2tools\helpers\Message;

\Yii::$app->session->setFlash(Message::TYPE_INFO, 'some message');
// or
Message::setSuccessMsg('success message');
// or
Message::setErrorMsg(['error1 message', 'error2 message']);
```

输出消息：

```php
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

## 在 view 如何更好的把 js 和 css 注入到布局

来源：

* [Yii2 如何更好的在页面注入 JavaScript](https://getyii.com/topic/9)

* [Yii2 如何更好的在页面注入 CSS](https://getyii.com/topic/10)

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
