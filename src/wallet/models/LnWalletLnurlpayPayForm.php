<?php
namespace lnpay\wallet\models;

use lnpay\components\HelperComponent;
use lnpay\models\BalanceWithdraw;
use lnpay\models\LnTx;
use lnpay\wallet\exceptions\UnableToPayLnurlpayException;
use lnpay\wallet\models\WalletTransaction;
use yii\base\Model;
use lnpay\models\User;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * LnWalletLnurlpayPayForm form
 */
class LnWalletLnurlpayPayForm extends Model
{
    public $amt_msat = NULL;
    public $lnurlpay_encoded = NULL;
    public $ln_address = null;
    public $_lnurlpay_decoded = NULL;
    public $_pr = NULL;
    public $probe_json = [];
    public $passThru = [];


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amt_msat','probe_json'], 'required'],
            ['lnurlpay_encoded', 'required', 'when' => function($model) { return empty($model->ln_address); }],
            ['ln_address', 'required', 'when' => function($model) { return empty($model->lnurlpay_encoded); }],
            [['amt_msat'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['lnurlpay_encoded'],'validLnurl'],
            [['ln_address'],'email'],
            [['amt_msat'],'amountCheck']
        ];
    }

    public function attributeLabels()
    {
        return [
            'lnurlpay_encoded'=>'Encoded LNURL Pay',
            'amt_msat'=>'Amount millisats'
        ];
    }

    public function validLnurl()
    {
        $decoded = \tkijewski\lnurl\decodeUrl($this->lnurlpay_encoded);
        if (isset($decoded['url'])) {
            $this->_lnurlpay_decoded = $decoded['url'];
        } else {
            $this->addError('lnurlpay_encoded','could not decode lnurlpay');
        }

    }

    public function amountCheck()
    {
        if ($this->amt_msat > $this->probe_json['maxSendable']) {
            $this->addError('amt_msat','LNURL cannot accept more than '.$this->probe_json['maxSendable'].' msat');
        }
        if ($this->amt_msat < $this->probe_json['minSendable']) {
            $this->addError('amt_msat','LNURL cannot accept less than '.$this->probe_json['minSendable'].' msat');
        }
    }

    public function requestRemoteInvoice()
    {
        $client = new \GuzzleHttp\Client([
            'curl'=> [],
            'http_errors'=>true,
            'headers' => ['SERVICE'=>'LNPAY'],
            'debug'=>false
        ]);

        $r = null;
        $lnurl = $this->probe_json['callback'] . (stripos($this->probe_json['callback'],'?')!==FALSE?'&':'?');
        $url = $lnurl.'amount='.$this->amt_msat;
        $response = $client->request('GET', $url);
        $r = $response->getBody()->getContents();
        $r = json_decode($r,TRUE);

        if (isset($r['pr'])) { //clear to pay this invoice
            return $r['pr'];
        } else if (@$r['error']) {
            throw new UnableToPayLnurlpayException('Lnurlpay service:'.$r['reason']);
        }

        throw new UnableToPayLnurlpayException('Could not retrieve pr from '.$url);
    }
}
