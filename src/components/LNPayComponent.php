<?php
namespace lnpay\components;


use lnpay\helpers\QRImageWithText;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\web\Response;


class LNPayComponent extends Component
{

    public static function genQrWithLabel($str,$text=null)
    {
        if (!$text)
            $text = \LNPay::$app->name;

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
        $response->data = $qrOutputInterface->dump(null, $text);
        return $response;
        //$response->send();
    }


    public static function processTz($user)
    {
        if (\LNPay::$app instanceof \yii\web\Application) {
            if ($tz = \LNPay::$app->session->get('tz')) {
                date_default_timezone_set($tz);
                return $tz;
            } else if ($tz = $user->tz) {
                \LNPay::$app->session->set('tz',$tz);
                date_default_timezone_set($tz);
                return $tz;
            } else {
                $ip = \LNPay::$app->request->getUserIP();
                $ipInfo = @file_get_contents('https://ipinfo.io/'.$ip);
                $ipInfo = json_decode($ipInfo, true);
                $tz = (@$ipInfo['timezone']?:'UTC');

                \LNPay::debug('ipinfo:'.print_r($ipInfo,TRUE),__METHOD__);
                $user->setTimeZone($tz);
                date_default_timezone_set($tz);
                return $tz;
                }
        } else {
            return $user->tz;
        }

    }
}
