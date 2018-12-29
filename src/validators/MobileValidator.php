<?php

namespace hubeiwei\yii2tools\validators;

use yii\validators\RegularExpressionValidator;

class MobileValidator extends RegularExpressionValidator
{
    public $pattern = "/^1([38][0-9]|4[5-9]|5[0-35-9]|66|7[0-8]|9[8-9])\d{8}$/";
    public $message = '手机号格式无效';
}
