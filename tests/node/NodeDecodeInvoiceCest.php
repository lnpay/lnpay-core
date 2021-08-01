<?php


use lnpay\fixtures\UserFixture;

class NodeDecodeInvoiceCest
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
            'user_access_key' => [
                'class' => \lnpay\fixtures\UserAccessKeyFixture::class,
            ],
            'ln_node' => [
                'class'=>\lnpay\node\fixtures\LnNodeFixture::class
            ]
        ];
    }

    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    public function LndDecodeInvoice(\ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->haveHttpHeader('X-Api-Key', 'sak_KkKkKkKkKkneieivTI05Fm3YzTza4N');
        $I->sendGET('/v1/node/lnod_bob/payments/decodeinvoice',[
            'payment_request'=>'lnbcrt690n1pssq8zwpp5z29qr8cny73j70sa3nqk33swa75j50qzmvm3nuuca2tf92jqjqqqdqqcqzpgxqyz5vqsp50l5jnduwkcj7z7wg2vhhf3faglr2ev8un0z5rs7ae2xh8gwdm4zq9qyyssqhl4mgfge847w0efm3cww5qfyxce8sky0uc9ymdeusqg3gekwm25kwd94p6gamty6t8vshmfyv9nxrpdwcf7wc2my240v3urvrak8e5qpqmpc06'
        ]);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseContains('"destination":"037ab245cff7f54f76e605327d93e18ed641bddd49e3e46dafb13a5a646c25a041"');
    }
}
