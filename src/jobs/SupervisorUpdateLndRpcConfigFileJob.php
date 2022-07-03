<?php
namespace lnpay\jobs;

use lnpay\components\SupervisorComponent;

class SupervisorUpdateLndRpcConfigFileJob extends \yii\base\BaseObject implements \yii\queue\JobInterface
{
    public $listener_id;
    public $parameters;

    public function execute($queue)
    {
        SupervisorComponent::updateLndRpcConfigFile($this->listener_id,$this->parameters);
    }
}