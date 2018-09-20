# yii2-tools

使用 yii2 积累下来的一些东西，并不是为了有多好用，仅仅是为了减少每次调用时要写的代码罢了。

## 目录

* [安装](#安装)

* [查询](#查询)

    * [准备工作](#准备工作)

    * [开始使用](#开始使用)

    * [日期范围查询的配置](#日期范围查询的配置)

* [验证器](#验证器)

* [Widget](#widget)

* [消息提示](#消息提示)

* [在 View 如何更好的把 js 和 css 注入到布局](#在-view-如何更好的把-js-和-css-注入到布局)

* [打赏](#打赏)

## 安装

执行：

```
composer require hubeiwei/yii2-tools 2.0.x-dev
```

或者添加：

```
"hubeiwei/yii2-tools": "2.0.x-dev"
```

因为是我自己用的东西，灵活性不一定高，如果你觉得这些代码不能100%满足你，你需要进行一些改动的话，你可以直接把代码下载到 vendor 目录外，添加：

```
"autoload": {
    "psr-4": {
        "hubeiwei\\yii2tools\\": "path/to/yii2-tools"
    }
}
```

然后把该项目的 composer.json 文件里 require 的包都加到你自己的 composer.json 里，执行 `composer update`。

## 查询

### 准备工作

以下3种方法自选一种：

1:如果你 model 继承的类还是 `yii\db\ActiveRecord`，你可以改成 `hubeiwei\yii2tools\db\ActiveRecord`。

2:如果你已经有了自己的 `ActiveRecord` 类，但并没有 `ActiveQuery` 类，你可以把 `find()` 方法改成以下代码：

```php
public static function find()
{
    return Yii::createObject('hubeiwei\yii2tools\db\ActiveQuery', [
        get_called_class(),
        [
            'timeRangeSeparator' => '-',
        ],
    ]);
}
```

3:如果你已经有了自己的 `ActiveQuery` 和 `Query` 类，你可以直接引入我的 trait：`\hubeiwei\yii2tools\db\ActiveQuery\QueryTrait`。

### 开始使用

实例化 `ActiveQuery` 或 `Query`：

```php
$query = \common\models\User::find();
// or
$query = new \hubeiwei\yii2tools\db\Query([
    'timeRangeSeparator' => '-',
]);
```

数字范围查询：

```php
// WHERE money = 1
$query->compare('money', 1);

// WHERE money > 1 AND money < 3 AND money = 2
$query->compare('money', '>1,,<3 =2');
```

日期范围查询：

```php
$dateRange = '2017/01/01 - 2018/01/01';

// WHERE time BETWEEM 1483200000 AND 1514822399
$query->timeRangeFilter('time', $dateRange, true, true);

// WHERE time BETWEEM '2017/01/01 00:00:00' AND '2018/01/01 23:59:59'
$query->timeRangeFilter('time', $dateRange, true, false);
```

时间范围查询：

```php
$dateTimeRange = '2017/01/01 01:01:01 - 2018/01/01 23:59:59';

// WHERE time BETWEEM 1483203661 AND 1514822399
$query->timeRangeFilter('time', $dateTimeRange, false, true);

// WHERE time BETWEEM '2017/01/01 01:01:01' AND '2018/01/01 23:59:59'
$query->timeRangeFilter('time', $dateTimeRange, false, false);
```

### 日期范围查询配置

日期范围查询的默认分割字符串是“-”，如果你想修改这个配置，方法如下：

1:参考[准备工作](#准备工作)里的第2条，单这里就有至少3种做法。

2:DI 容器，如果你是用 `Yii::createObject()` 实例化 `ActiveQuery`，在你的 bootstrap.php 文件添加以下代码：

```php
// 这里配置的类需要根据你具体用到的 `ActiveQuery` 类而定。
Yii::$container->set('hubeiwei\yii2tools\db\ActiveQuery', [
    'timeRangeSeparator' => '-',
]);
```

3:`Query` 类需要在实例化的时候修改，参考[开始使用](#开始使用)的实例化部分。

## 验证器

目前提供了3个验证器：

* 中文验证器：`hubeiwei\yii2tools\validators\ChineseValidator`

* 身份证验证器：`hubeiwei\yii2tools\validators\IdCardValidator`

* 手机号验证器：`hubeiwei\yii2tools\validators\MobileValidator`

常规用法：

```php
use hubeiwei\yii2tools\validators\ChineseValidator;

$chineseValidator = new ChineseValidator();
if (!$chineseValidator->validate($value)) {
    // 必须包含中文
}
$chineseValidator->chineseOnly = true;
if (!$chineseValidator->validate($value)) {
    // 必须为纯中文
}
```

在 rules 中使用：

```php
use hubeiwei\yii2tools\validators\IdCardValidator;

['id_card', IdCardValidator::class],
```

## Widget

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
    if ($value === null) {
        return $map;
    }
    return ArrayHelper::getValue($map, $value);
}
```

以下是一个视图的代码：

```php
use common\models\User;
use hubeiwei\yii2tools\grid\ActionColumn;
use hubeiwei\yii2tools\grid\ExportMenu;
use hubeiwei\yii2tools\grid\SerialColumn;
use hubeiwei\yii2tools\helpers\Render;
use hubeiwei\yii2tools\widgets\DateRangePicker;
use hubeiwei\yii2tools\widgets\Select2;

/**
 * @var $this yii\web\View
 * @var $searchModel app\models\search\ArticleSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = '标题';

$gridColumns = [
    ['class' => SerialColumn::className()],

    // 枚举字段查询
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

    // 时间范围选择插件
    [
        'attribute' => 'created_at',
        'format' => 'dateTime',
        'filterType' => DateRangePicker::className(),
        /*'filterWidgetOptions' => [
            'dateOnly' => true,
            'dateFormat' => 'Y/m/d',
            'separator' => ' - ',// 日期分隔符，配合日期范围查询使用
        ],*/
    ],

    ['class' => ActionColumn::className()],
];

// GridView
echo Render::gridView([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'export' => [
        'exportConfig' => [
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'pjaxContainerId' => 'kartik-grid-pjax',
    ],
]);

// DynaGrid
echo Render::dynaGrid([
    // 'id' => 'user-index',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'export' => true,
]);
```

> DynaGrid 需要配置模块才能使用，具体看[官方文档](https://github.com/kartik-v/yii2-dynagrid#module)，或者参考[我的](https://github.com/hubeiwei/hello-yii2/blob/master/config/modules.php#L40)。

本节示例插件的 DEMO：

* [Select2](http://demos.krajee.com/widget-details/select2)
* [DateRangePicker](http://demos.krajee.com/date-range)
* [GridView](http://demos.krajee.com/grid-demo)
* [DynaGrid](http://demos.krajee.com/dynagrid-demo)
* [Export](http://demos.krajee.com/export-demo)

## 消息提示

设置消息：

```php
use hubeiwei\yii2tools\helpers\Message;

\Yii::$app->session->setFlash(Message::TYPE_INFO, 'some message');
// or
Message::setSuccessMsg('操作成功');
// or
Message::setErrorMsg(['错误1', '错误2']);
```

输出消息：

```php
use hubeiwei\yii2tools\widgets\Alert;
use hubeiwei\yii2tools\widgets\Growl;

echo Alert::widget();
// or
echo Growl::widget();
```

> 建议放在 layout 里，一劳永逸

本节示例插件的 DEMO：

* [Alert](https://v3.bootcss.com/components/#alerts-dismissible)：这个没啥好说的，就是 bootstrap 的可关闭 Alert
* [Growl](http://demos.krajee.com/widget-details/growl)：进去之后看到一个表单，提交后可以看到效果

## 在 View 如何更好的把 js 和 css 注入到布局

来源：

* [Yii2 如何更好的在页面注入 JavaScript](https://getyii.com/topic/9)

* [Yii2 如何更好的在页面注入 CSS](https://getyii.com/topic/10)

```php
<?php

use hubeiwei\yii2tools\widgets\CssBlock;
use hubeiwei\yii2tools\widgets\JsBlock;
use yii\web\View;
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

如果觉得我做的东西对你有帮助的话，可以随意打赏一下，这样我会有更多动力去分享更多 yii2 的内容。

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/ali_pay.jpg" width="500px" alt="支付宝">

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/wechat_pay.png" width="500px" alt="微信">

感谢以下这些朋友的支持。

打赏人 | QQ | 金额
---|---|---
若 | 921520651 | 88.88
River° | 347742286 | 65.00
誓言 | 443536249 | 50.00
山中石 | 1146283 | 50.00
N | 1024720263 | 50.00
东方不拔 | 790292520 | 30.00
allen | 2134691391 | 20.00
欲买桂花同载酒。 | 1054828207 | 18.88
一缕云烟 | 494644368 | 13.80
[a boy with a mission](https://github.com/xiaocai314) | 727492986 | 8.88
hello world! | 85307097 | 1.00
