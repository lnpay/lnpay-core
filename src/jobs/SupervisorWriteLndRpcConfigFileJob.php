<?php
namespace lnpay\jobs;

use lnpay\components\SupervisorComponent;

class SupervisorWriteLndRpcConfigFileJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $config_filename;
    public $listeners = [];

    public function execute($queue)
    {
        SupervisorComponent::writeLndRpcConfigFile($this->config_filename,$this->listeners);
    }
}