<?php
namespace app\components\node;


use Yii;
use yii\base\Component;


abstract class LnBaseNodeClass extends Component
{
    protected $endpoint = '';
    protected $apiVersion = '';
    protected $tlsCert = '';
    protected $macaroonHex = '';


}