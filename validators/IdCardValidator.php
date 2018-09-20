<?php

namespace hubeiwei\yii2tools\validators;

use yii\validators\RegularExpressionValidator;

class IdCardValidator extends RegularExpressionValidator
{
    public $pattern = "/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X|x)$/";
    public $message = '身份证格式无效';
}
