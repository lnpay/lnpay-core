<?php


use lnpay\fixtures\UserFixture;

class LnurlWithdrawProcessPublicCest
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

    public $lnurlStatic;

    public function _before(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/wallet/wal_aliceLnurlWithdraw/lnurl/withdraw?memo=Tester&num_satoshis=69');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"lnurl":"LNURL');
        $I->seeResponseContains('"ott":');
        $lnurlStatic = $I->grabDataFromResponseByJsonPath('$.lnurl');
        expect_that($decodedLnurl = tkijewski\lnurl\decodeUrl($lnurlStatic[0]));
        expect_that($this->lnurlStatic = $decodedLnurl['url']);
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
}
