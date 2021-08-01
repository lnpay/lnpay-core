<?php
namespace lnpay\controllers;

use lnpay\components\HelperComponent;
use lnpay\components\LNPayComponent;
use lnpay\models\BaseLink;
use lnpay\models\BaseLinkAnalytics;
use lnpay\models\DistroMethod;
use lnpay\models\StatusType;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use lnpay\helpers\QRImageWithText;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

use \Mobile_Detect;

/**
 * Home controller
 */
class DistroRouterController extends Controller
{
    public function beforeAction($event)
    {
        return parent::beforeAction($event);
    }

    public function actionSplice($short_url,$distro_name='web')
    {
        $this->_passThruParams = \LNPay::$app->request->getQueryParams();

        //Don't include referrals from our own sites
        $bl = BaseLink::find()->where(['short_url'=>$short_url])->one();
        if ($bl) {
            $this->_baseLink = $bl;

            $product = $bl->productObject;
            if (!$product) {
                throw new BadRequestHttpException('Invalid short url');
            }

            switch ($bl->link_type_id) {
                case 1:
                    break;
            }

        } else {
            return $this->redirect('/');
        }
    }


    /*********************************************************
     *
     * FAUCET FUNCTIONS END!
     *
     *********************************************************/

    public function actionQr($str,$a='')
    {
        $append='';
        if (stripos($str,'lnbc')!==FALSE) {
            $append .= ' LN Invoice';
        } else if (stripos($str,'lnurl')!==FALSE) {
            $append .= ' LNURL';
        }
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'imageBase64' => false,

        ]);
        $qrImage = (new QRCode($options));
        $qrOutputInterface = new QRImageWithText($options, $qrImage->getMatrix($str));

        $response = \LNPay::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->headers->set("Pragma-directive: no-cache");
        $response->headers->set("Cache-directive: no-cache");
        $response->headers->set("Cache-control: no-cache");
        $response->headers->set("Pragma: no-cache");
        $response->headers->set("Expires: 0");
        $response->format = Response::FORMAT_RAW;
        $response->data = $qrOutputInterface->dump(null, \LNPay::$app->name.$append.' '.$a);
        return $response->send();
    }

}