<?php
namespace lnpay\node\components;

use lnpay\components\HelperComponent;
use Yii;
use yii\base\Component;
use yii\helpers\VarDumper;

/**
 * Node Add Form
 */
class LnMacaroonObject extends Component
{
    private $_macaroonRaw;
    private $_permissions = [];

    /**
     * MUST BE KEPT UP TO DATE WITH: https://github.com/lightningnetwork/lnd/blob/master/rpcserver.go#L199
     */
    private $_validEntities = ['onchain','offchain','address','message','peers','info','invoices','signer','macaroon'];
    private $_validActions = ['read','write','generate'];

    /**
     * LnMacaroonObject constructor.
     * @param $macaroonHexOrRaw
     */
    public function __construct($macaroonHexOrRaw=NULL)
    {
        parent::__construct();
        if ($macaroonHexOrRaw) {
            $macaroonHexOrRaw = preg_replace('/[\r\n]+/','', $macaroonHexOrRaw);
            if (ctype_xdigit($macaroonHexOrRaw)) {

                $this->_macaroonRaw = hex2bin($macaroonHexOrRaw);
            }
            else
                $this->_macaroonRaw = $macaroonHexOrRaw;

            $this->_permissions = $this->decodePermsFromMacaroon($this->_macaroonRaw);
        }
    }

    public function decodePermsFromMacaroon($macaroonRaw)
    {
        $array = [];
        $lines = explode("\n",$macaroonRaw);
        foreach ($lines as $l) {
            foreach ($this->validEntities as $vE) {
                if (stripos($l,$vE) !== FALSE) {
                    $array[$vE] = [];
                    foreach ($this->validActions as $vA) {
                        if (stripos($l,$vA) !== FALSE) {
                            $array[$vE][] = $vA;
                        }
                    }
                }
            }
        }
        return $array;
    }

    /**
     * Return array of valid entities
     * @param null $entity
     * @return array|bool
     */
    public function getValidEntities($entity=NULL)
    {
        if ($entity) {
            return in_array($entity,$this->_validEntities);
        }

        return $this->_validEntities;
    }

    /**
     * Return array of valid actions
     * @param null $action
     * @return array|bool
     */
    public function getValidActions($action=NULL)
    {
        if ($action) {
            return in_array($action,$this->_validActions);
        }
        return $this->_validActions;
    }

    /**
     * Return array of all valid permission combinations
     * e.g. [ ['onchain' => ['read','write','generate']], ['offchain' => ['read','write','generate']]  .... ]
     * @return array
     */
    public static function getAllowedPermissionMap()
    {
        $m = new self();
        $arr = [];
        foreach ($m->validEntities as $ve) {
            foreach ($m->validActions as $va) {
                $arr[$ve][] = $va;
            }
        }
        return $arr;
    }

    /**
     * @param $macaroonRaw
     * @return string
     */
    public static function getHexFromRaw($macaroonRaw)
    {
        return bin2hex($macaroonRaw);
    }

    /**
     * @return string
     */
    public function getHex()
    {
        return static::getHexFromRaw($this->_macaroonRaw);
    }

    /**
     * @return string|string[]|null
     */
    public function getRaw()
    {
        return $this->_macaroonRaw;
    }

    /**
     * @return string
     */
    public function getBase64()
    {
        return base64_encode($this->_macaroonRaw);
    }

    /**
     * @return string
     */
    public function getBase64url()
    {
        return HelperComponent::base64url_encode($this->_macaroonRaw);
    }

    /**
     * @param $permArray
     */
    public function setPermissions($permArray)
    {
        $this->_permissions = $permArray;
    }

    /**
     * Parse macaroon for permissions
     * [['Entity'=>['action','action']]
     * @return array
     */
    public function getPermissions()
    {
        return $this->_permissions;
    }

    /**
     * @param $entity
     * @param $action
     * @return bool
     */
    public function hasPerm($entity,$action)
    {
        $permissions = $this->permissions;
        if ($entity = @$permissions[$entity]) {
            if (in_array($action,$entity))
                return TRUE;
        }
        return FALSE;
    }

    /**
     * @return string
     */
    public function generateLncliBakeCommand()
    {
        $cmd = 'lncli bakemacaroon ';
        foreach ($this->_permissions as $entity => $actions) {
            foreach ($actions as $action) {
                $cmd .= "$entity:$action ";
            }
        }

        return trim($cmd);
    }

    /**
     * @param $array
     * @param string $newLine
     * @return string
     */
    public static function permissionArrayToReadableString($array,$newLine=' ')
    {
        $str = '';
        foreach ($array as $entity => $perms) {
            foreach ($perms as $p) {
                $str .= $entity.':'.$p. $newLine;
            }
        }
        return $str;
    }

    /**
     * @return string
     */
    public function readableString()
    {
        return static::permissionArrayToReadableString($this->getPermissions());
    }

    /**
     * @return bool
     */
    public function getIsValidMacaroon()
    {
        if (!array_filter($this->getPermissions())) {
            return false;
        } else {
            return TRUE;
        }
    }

}