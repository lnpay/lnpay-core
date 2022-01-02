<?php
namespace lnpay\components;


use lnpay\behaviors\UserAccessKeyBehavior;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;


class HelperComponent extends Component
{
    /*
     * https://stackoverflow.com/questions/3711357/getting-title-and-meta-tags-from-external-website
     *
     */
    public static function file_get_contents_curl($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function getErrorStringFromInvalidModel($model) {
        $errors = $model->getErrors();
        $errorStr = 'Invalid Attribute :(';
        foreach ($errors as $attr => $errorArray) {
            $errorStr = $errorArray[0];
        }
        return $errorStr;
    }


    public static function encodeTestInvoice($params=[])
    {
        return base64_encode(json_encode($params));
    }

    public static function decodeTestInvoice($str)
    {
        return @json_decode(@base64_decode($str),TRUE);
    }

    public static function generateRandomString($length=32) {
        return str_replace(['-','_'],'',\LNPay::$app->security->generateRandomString($length));
    }

    public static function getRolePrefix($role)
    {
        //Manual overrides
        switch ($role) {
            case UserAccessKeyBehavior::ROLE_WALLET_ADMIN:
                return 'waka';
                break;
            case UserAccessKeyBehavior::ROLE_WALLET_INVOICE:
                return 'waki';
                break;
            case UserAccessKeyBehavior::ROLE_WALLET_READ:
                return 'wakr';
                break;
            case UserAccessKeyBehavior::ROLE_WALLET_LNURL_WITHDRAW:
                return 'waklw';
            case UserAccessKeyBehavior::ROLE_WALLET_LNURL_PAY:
                return 'waklp';
                break;
        }

        $words = explode(" ", $role);
        $acronym = "";

        foreach ($words as $w) {
            $acronym .= $w[0];
        }

        return strtolower($acronym);
    }

    public static function generateKeyByRolePrefix($prefix)
    {
        switch ($prefix) {
            case 'sak':
            case 'pak':
                $length = 32;
                break;
            default:
                $length = 24;
        }

        return $prefix.'_'.self::generateRandomString($length);
    }

    //Ganked and modified from https://gist.github.com/stubbetje/422106
    public static function array_flatten( $array , $keySeparator = '__' )
    {
        if( is_array( $array ) ) {
            foreach( $array as $name => $value ) {
                $f = self::array_flatten( $value , $keySeparator );
                if( is_array( $f ) ) {
                    foreach( $f as $key => $val ) {
                        $array[ $name . $keySeparator . $key ] = $val;
                    }
                    unset( $array[ $name ] );
                }
            }
        }
        return $array;
    }

    public static function parseHeaderArrayToString($array)
    {
        $str = '';
        foreach ($array as $name => $values) {
            $str .= $name . ': ' . implode(', ', $values) . "\r\n";
        }
        return $str;
    }

    /**
     * If valid lndconnect string, good. otherwise false
     *
     * @param $string
     * @return array|bool|mixed
     *
     */
    public static function parseLndConnectString($string)
    {
        $r = parse_url($string);

        if (!@$r['scheme'] == 'lndconnect')
            return false;

        if (@$r['query']) {
            parse_str($r['query'],$array);
            $r = ArrayHelper::merge($r,$array);
            unset($r['query']);
        } else {
            return false;
        }

        if (@$r['host'] &&
            @$r['port'] &&
            @$r['macaroon'] &&
            @$r['cert'])
            return $r;
        else
            return false;
    }

    public static function base64url_decode($str)
    {
        return base64_decode(str_pad(strtr($str, '-_', '+/'), strlen($str) % 4, '=', STR_PAD_RIGHT));
    }

    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function array_diff_assoc_recursive($array1, $array2)
    {
        $difference = array();
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (!isset($array2[$key]) || !is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = static::array_diff_assoc_recursive($value, $array2[$key]);
                    if (!empty($new_diff)) {
                        $difference[$key] = $new_diff;
                    }
                }
            } else {
                if (!array_key_exists($key, $array2) || $array2[$key] !== $value) {
                    $difference[$key] = $value;
                }
            }
        }
        return $difference;
    }

    public static function str_contains($str, array $arr)
    {
        foreach($arr as $a) {
            if (stripos($str,$a) !== false) return true;
        }
        return false;
    }

    public static function array_to_xml(array $arr, \SimpleXMLElement $xml)
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                static::array_to_xml($v, $xml->addChild($k));
            } else {
                $xml->addChild($k, htmlspecialchars($v));
            }
        }
        return $xml;
    }

    public static function getAliasFromPubkey($pubkey) {
        return false;
        $r = @file_get_contents('https://1ml.com/node/'.$pubkey.'/json');
        $r = @json_decode($r,TRUE);
        return @$r['alias'];
    }

}
