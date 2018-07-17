<?php

namespace hubeiwei\yii2tools\helpers;

use Yii;
use yii\bootstrap\Widget;

class Message extends Widget
{
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';

    /**
     * @param string|array $message
     * @param string $type
     */
    public static function setMessage($message, $type = self::TYPE_INFO)
    {
        Yii::$app->getSession()->setFlash($type, $message);
    }

    public static function setSuccessMsg($message)
    {
        self::setMessage($message, self::TYPE_SUCCESS);
    }

    public static function setErrorMsg($message)
    {
        self::setMessage($message, self::TYPE_ERROR);
    }

    public static function setDangerMsg($message)
    {
        self::setMessage($message, self::TYPE_DANGER);
    }

    public static function setWarningMsg($message)
    {
        self::setMessage($message, self::TYPE_WARNING);
    }
}
