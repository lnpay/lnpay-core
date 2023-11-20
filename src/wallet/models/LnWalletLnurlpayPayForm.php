<?php
namespace lnpay\wallet\models;

use lnpay\components\HelperComponent;
use lnpay\models\BalanceWithdraw;
use lnpay\models\LnTx;
use lnpay\wallet\controllers\api\v1\LnurlpayController;
use lnpay\wallet\exceptions\InvalidLnurlpayLinkException;
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
    public $comment = NULL;


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
            [['amt_msat'],'amountCheck'],
            [['comment'],'string'],
            [['comment'],'commentCheck']
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

    public function commentCheck()
    {
        $commentAllowed = @$this->probe_json['commentAllowed'];
        if ($this->comment && $commentAllowed) {
            if (strlen($this->comment) > $commentAllowed) {
                $this->addError('comment','Comment length is too long ('.strlen($this->comment).') - endpoint supports ('.$commentAllowed.') characters');
            }
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

        if ($this->comment) {
            $url .= '&comment='.$this->comment;
        }

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

    public static function probe($lnurlpayEncodedOrLnAddress)
    {
        try {
            if (stripos($lnurlpayEncodedOrLnAddress,'@')!==FALSE) {
                $url = static::getUrlFromLnAddress($lnurlpayEncodedOrLnAddress);
            } else if ($lnurlp = \tkijewski\lnurl\decodeUrl($lnurlpayEncodedOrLnAddress)) {
                if (@$lnurlp['url']) {
                    $url = $lnurlp['url'];
                } else {
                    throw new InvalidLnurlpayLinkException('invalid lnurlpay link');
                }
            } else {
                throw new \Exception('lnurlpay_encoded or ln_address must be specified');
            }

            $client = new \GuzzleHttp\Client([
                'curl'=> [],
                'http_errors'=>true,
                'headers' => ['SERVICE'=>'LNPAY-PROBE'],
                'debug'=>false
            ]);

            $r = null;
            $response = $client->request('GET', $url);
            $r = $response->getBody()->getContents();

        } catch (\Throwable $t) {
            throw new InvalidLnurlpayLinkException('Invalid Address / LNURL');
        }

        $json = json_decode($r,TRUE);
        if (@$json['metadata'])
            $json['metadata'] = json_decode($json['metadata'],TRUE);

        return $json;
    }

    public static function getUrlFromLnAddress($lnAddress)
    {
        $username = explode('@',$lnAddress)[0];
        $domain = explode('@',$lnAddress)[1];
        if (YII_ENV_TEST)
            $url = 'http://localhost/index-test.php/.well-known/lnurlp/'.$username;
        else
            $url = 'https://'.$domain.'/.well-known/lnurlp/'.$username;

        return $url;
    }
}
