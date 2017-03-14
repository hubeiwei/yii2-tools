<?php

namespace hubeiwei\yii2tools\widgets;

use hubeiwei\yii2tools\helpers\Message;
use kartik\growl\Growl as KartikGrowl;
use Yii;
use yii\bootstrap\Widget;

/**
 * 输出消息用，建议放在 layout 里
 * ```php
 * <?= hubeiwei\yii2tools\widgets\Growl::widget() ?>
 * ```
 *
 * 设置消息请看 Message 类
 * @see Message
 */
class Growl extends Widget
{
    /**
     * @var array
     */
    public $typeMap = [
        Message::TYPE_INFO => KartikGrowl::TYPE_INFO,
        Message::TYPE_SUCCESS => KartikGrowl::TYPE_SUCCESS,
        Message::TYPE_ERROR => KartikGrowl::TYPE_DANGER,
        Message::TYPE_DANGER => KartikGrowl::TYPE_DANGER,
        Message::TYPE_WARNING => KartikGrowl::TYPE_WARNING,
    ];

    /**
     * @var array
     */
    public $iconMap = [
        Message::TYPE_INFO => 'glyphicon glyphicon-info-sign',
        Message::TYPE_SUCCESS => 'glyphicon glyphicon-ok-sign',
        Message::TYPE_ERROR => 'glyphicon glyphicon-remove-sign',
        Message::TYPE_DANGER => 'glyphicon glyphicon-remove-sign',
        Message::TYPE_WARNING => 'glyphicon glyphicon-exclamation-sign',
    ];

    /**
     * @var string
     */
    private $_message = '';

    public function init()
    {
        $session = Yii::$app->getSession();
        $flashes = $session->getAllFlashes();
        foreach ($flashes as $type => $data) {
            if (isset($this->typeMap[$type])) {
                $data = (array)$data;
                foreach ($data as $i => $message) {
                    $this->_message .= KartikGrowl::widget([
                        'type' => $this->typeMap[$type],
                        'icon' => $this->iconMap[$type],
                        'body' => $message,
                        'pluginOptions' => [
                            'showProgressbar' => true,
                            'placement' => [
                                'from' => 'top',
                                'align' => 'center',
                            ]
                        ]
                    ]);
                }
                $session->removeFlash($type);
            }
        }
    }

    public function run()
    {
        return $this->_message;
    }
}
