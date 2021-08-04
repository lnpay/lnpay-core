<?php


use lnpay\fixtures\UserFixture;

class LnurlWithdrawProcessDisposableCest
{
    public function _fixtures()
    {
        return [
            'users' => [
                'class' => UserFixture::class,
            ],
            'wallets' => [
                'class' => \lnpay\fixtures\WalletFixture::class,
            ],
            'wallet_transactions' => [
                'class' => \lnpay\fixtures\WalletTransactionFixture::class,
            ],
            'lntx' => [
                'class' => \lnpay\fixtures\LnTxFixture::class,
            ],
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ]
        ];
    }

    public $lnurlDisposable;

    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wal_aliceLnurlWithdraw/lnurl/withdraw?memo=Tester&num_satoshis=69');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"lnurl":"LNURL');
        $I->seeResponseContains('"ott":');
        $lnurlDisposable = $I->grabDataFromResponseByJsonPath('$.lnurl');
        expect_that($decodedLnurl = tkijewski\lnurl\decodeUrl($lnurlDisposable[0]));
        expect_that($this->lnurlDisposable = $decodedLnurl['url']);
    }

    public function _after(ApiTester $I)
    {

    }

    /**
     * @param int[] $params
     * @return object $object->paymentRequest to get payment request
     * @throws \lnpay\node\exceptions\UnableToCreateInvoiceException
     */
    public function generateTestInvoice($params=['value'=>2])
    {
        expect_that($node = \lnpay\node\models\LnNode::findOne('lnod_bob'));
        expect_that($generated_invoice = (object) $node->getLndConnector('RPC')->createInvoice($params));
        expect_that($generated_invoice = $generated_invoice->paymentRequest);

        return $generated_invoice;
    }

    public function getLnurlProcessPreFetchSuccess(\ApiTester $I)
    {
        codecept_debug($this->lnurlDisposable);
        $I->sendGET($this->lnurlDisposable);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"callback":');
        $I->seeResponseContains('"maxWithdrawable":69000');
    }

    public function getLnurlProcessFulfillPrSuccess(\ApiTester $I)
    {
        codecept_debug($this->lnurlDisposable);
        $I->sendGET($this->lnurlDisposable);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"callback":');
        $I->seeResponseContains('"maxWithdrawable":69000');
        expect_that($lnurl = $I->grabDataFromResponseByJsonPath('$.callback')[0]);
        $lnurl = $lnurl . (stripos($lnurl,'?')!==FALSE?'&':'?');
        codecept_debug('callbackUrl:'.$lnurl);

        //Fetch an available invoice
        expect($invoice = $this->generateTestInvoice(['value'=>69]));

        //try and get PR paid
        $I->sendGET($lnurl.'pr='.$invoice);
        $I->seeResponseContains('"OK"');

        //try again, since this is dispoable, should fail
        expect($invoice = $this->generateTestInvoice(['value'=>69]));
        $I->sendGET($lnurl.'pr='.$invoice);
        $I->seeResponseContains('"ERROR"');
    }

    public function getLnurlProcessFulfillPrFail(\ApiTester $I)
    {
        codecept_debug($this->lnurlDisposable);
        $I->sendGET($this->lnurlDisposable);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"callback":');
        $I->seeResponseContains('"maxWithdrawable":69000');
        expect_that($lnurl = $I->grabDataFromResponseByJsonPath('$.callback')[0]);
        $lnurl = $lnurl . (stripos($lnurl,'?')!==FALSE?'&':'?');

        //Fetch an available invoice
        expect($invoice = $this->generateTestInvoice(['value'=>2422224]));
        $I->sendGET($lnurl.'pr='.$invoice);
        $I->seeResponseContains('"ERROR"');
    }
}
