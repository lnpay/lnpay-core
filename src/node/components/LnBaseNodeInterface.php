<?php
namespace lnpay\node\components;


use lnpay\node\models\LnNode;
use Yii;
use yii\base\Component;


interface LnBaseNodeInterface
{
    public static function initConnector(LnNode $lnNodeObject);

    public function createInvoice($invoiceOptions);
    public function checkInvoice($request);
    public function decodeInvoice($request);
    public function payInvoice($request);
    public function getInfo();

}