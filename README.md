# yii2-tools

自己在 yii2 上封装的一些东西，在公司也这么用，虽然不能满足所有人的口味，但如果你觉得好的话，你可以拿来用。以后我会花时间把这些东西做成比较通用可配置的。

## 安装

执行：

```
composer require hubeiwei/yii2-tools dev-master
```

或者添加：

```
"hubeiwei/yii2-tools": "dev-master"
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

建设中……

## 打赏

如果觉得我做的东西对你有帮助的话，求打赏一杯 coffee，这样我会有更多动力去分享更多 yii2 的内容。

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/ali_pay.jpg" width="500px" alt="支付宝">

<img src="https://raw.githubusercontent.com/hubeiwei/hubeiwei.github.io/master/images/pay/wechat_pay.png" width="500px" alt="微信">
