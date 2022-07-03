<?php

namespace tests\unit\components\node;


use lnpay\components\HelperComponent;
use lnpay\node\components\LnMacaroonObject;

class LnMacaroonObjectTest extends \Codeception\Test\Unit
{
    const MAC_HEX = '0201036c6e64029a01030a100e994b1307d522753c4d9cc9488277481201301a0f0a07616464726573731204726561641a0c0a04696e666f1204726561641a100a08696e766f696365731204726561641a0f0a076d6573736167651204726561641a100a086f6666636861696e1204726561641a0f0a076f6e636861696e1204726561641a0d0a0570656572731204726561641a0e0a067369676e657212047265616400000620f8882138b67979613e70fa8c37f43d80a27688581949c0a6d7b5856c655a4f3e';


    /**
     * @var \UnitTester
     */
    public $tester;

    public function testGetHexFromRaw()
    {
        expect(LnMacaroonObject::getHexFromRaw(hex2bin(self::MAC_HEX)))->equals(self::MAC_HEX);
    }

    public function testGetHex()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);
        expect($m->hex)->equals(self::MAC_HEX);

    }

    public function testGetBase64()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);
        expect($m->base64)->equals(base64_encode($m->raw));
    }

    public function testGetBase64url()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);
        expect($m->base64url)->equals(HelperComponent::base64url_encode($m->raw));

    }

    public function testGetRaw()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);
        expect($m->raw)->equals(hex2bin(self::MAC_HEX));
    }

    public function testGetPermissions()
    {
        $p = [
            'address'=>['read'],
            'info'=>['read'],
            'invoices'=>['read'],
            'message'=>['read'],
            'offchain'=>['read'],
            'onchain'=>['read'],
            'peers'=>['read'],
            'signer'=>['read']
        ];
        $m = new LnMacaroonObject(self::MAC_HEX);

        expect($m->permissions)->equals($p);
    }

    public function testGetAllowedPermissionsMap()
    {
        $p = [
            'address'=>['read','write','generate'],
            'info'=>['read','write','generate'],
            'invoices'=>['read','write','generate'],
            'message'=>['read','write','generate'],
            'offchain'=>['read','write','generate'],
            'onchain'=>['read','write','generate'],
            'peers'=>['read','write','generate'],
            'signer'=>['read','write','generate'],
            'macaroon'=>['read','write','generate'],
        ];

        expect(LnMacaroonObject::getAllowedPermissionMap())->equals($p);
    }

    public function testGetHasPerm()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);

        expect($m->hasPerm('invoices','read'))->true();
        expect($m->hasPerm('invoices','write'))->false();
    }

    public function testGenerateLncliBakeCommand()
    {
        $m = new LnMacaroonObject(self::MAC_HEX);
        expect($m->generateLncliBakeCommand())->equals('lncli bakemacaroon address:read info:read invoices:read message:read offchain:read onchain:read peers:read signer:read');
    }


}
