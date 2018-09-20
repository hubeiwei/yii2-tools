<?php

namespace hubeiwei\yii2tools\validators;

use yii\validators\RegularExpressionValidator;

class ChineseValidator extends RegularExpressionValidator
{
    public $chineseOnly = false;
    public $pattern = '/[\x{4e00}-\x{9fa5}]/u';
    public $message = '{attribute}必须包含汉字';
    public $strictPattern = '/^[\x{4e00}-\x{9fa5}]+$/u';
    public $strictMessage = '{attribute}必须是纯汉字';
    public $skipOnEmpty = false;

    public function init()
    {
        parent::init();
        if ($this->chineseOnly) {
            $this->pattern = $this->strictPattern;
            $this->message = $this->strictMessage;
        }
    }
}
