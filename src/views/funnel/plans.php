<?php

$this->title = 'LNPay Plans';

$plusActive = Yii::$app->request->getQueryParam('plan')=='plus';
$growthActive = Yii::$app->request->getQueryParam('plan')=='growth';
$proActive = Yii::$app->request->getQueryParam('plan')=='pro';

?>
<section class="pricing py-5">
    <div class="container">
        <div class="row">
            <!-- Pro Tier -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0 <?=$proActive?'bg-success':'';?>">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Pro</h5>
                        <h6 class="card-price text-center">$150+<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNPay Cloud API</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Generate / Pay LN Invoices</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>Unlimited</b> Wallets</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>Unlimited</b> Sat Invoice Limit</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>100,000+</b> Transactions/mo</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNURL</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Lightning Address</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Outbound Keysend</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Inbound Keysend</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Email/Telegram Support</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Dedicated LND Node</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Managed Liquidity</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Managed Channels</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Managed Backups</li>
                        </ul>
                        <?php if ($proActive) { ?>
                            <div class="d-grid text-center">
                                <a href="mailto:admin@lnpay.co?subject=cancel+plan" class="btn btn-danger text-uppercase">Cancel</a>
                            </div>
                        <?php } else { ?>
                            <div class="d-grid text-center">
                                <a href="<?php if (YII_ENV_DEV) echo 'https://buy.stripe.com/test_00gbLUfkt1Ai2li144'; else echo 'https://buy.stripe.com/aEU4htcr91DO2He4gr';?>" class="btn btn-primary text-uppercase">Upgrade to Plus</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <!-- Plus Tier -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0 <?=$growthActive?'bg-success':'';?>">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Growth</h5>
                        <h6 class="card-price text-center">$100<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNPay Cloud API</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Generate / Pay LN Invoices</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>500</b> Wallets</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>500,000</b> Sat Invoice Limit</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>10,000</b> Transactions/mo</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNURL</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Lightning Address</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Outbound Keysend</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Inbound Keysend</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Email/Telegram Support</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Dedicated LND Node</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Liquidity</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Channels</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Backups</li>
                        </ul>
                        <?php if ($growthActive) { ?>
                            <div class="d-grid text-center">
                                <a href="mailto:admin@lnpay.co?subject=cancel+plan" class="btn btn-danger text-uppercase">Cancel</a>
                            </div>
                        <?php } else { ?>
                            <div class="d-grid text-center">
                                <a href="<?php if (YII_ENV_DEV) echo 'https://buy.stripe.com/test_00gbLUfkt1Ai2li144'; else echo 'https://buy.stripe.com/aEU4htcr91DO2He4gr';?>" class="btn btn-primary text-uppercase">Upgrade to Plus</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Free Tier -->
            <div class="col-lg-4">
                <div class="card mb-5 mb-lg-0 <?=$plusActive?'bg-success':'';?>">
                    <div class="card-body">
                        <h5 class="card-title text-muted text-uppercase text-center">Plus</h5>
                        <h6 class="card-price text-center">$50<span class="period">/month</span></h6>
                        <hr>
                        <ul class="fa-ul">
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNPay Cloud API</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Generate / Pay LN Invoices</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>100</b> Wallets</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>250,000</b> Sat Invoice Limit</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span><b>1,000</b> Transactions/mo</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>LNURL</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Lightning Address</li>
                            <li><span class="fa-li"><i class="fas fa-check"></i></span>Outbound Keysend</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Inbound Keysend</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Email/Telegram Support</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Dedicated LND Node</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Liquidity</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Channels</li>
                            <li class="text-muted"><span class="fa-li"><i class="fas fa-times"></i></span>Managed Backups</li>
                        </ul>
                        <?php if ($plusActive) { ?>
                        <div class="d-grid text-center">
                            <a href="mailto:admin@lnpay.co?subject=cancel+plan" class="btn btn-danger text-uppercase">Cancel</a>
                        </div>
                        <?php } else { ?>
                        <div class="d-grid text-center">
                            <a href="<?php if (YII_ENV_DEV) echo 'https://buy.stripe.com/test_00gbLUfkt1Ai2li144'; else echo 'https://buy.stripe.com/aEU4htcr91DO2He4gr';?>" class="btn btn-primary text-uppercase">Upgrade to Plus</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
    .pricing .card {
        border: none;
        border-radius: 1rem;
        transition: all 0.2s;
        box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
    }

    .pricing hr {
        margin: 1.5rem 0;
    }

    .pricing .card-title {
        margin: 0.5rem 0;
        font-size: 0.9rem;
        letter-spacing: .1rem;
        font-weight: bold;
    }

    .pricing .card-price {
        font-size: 3rem;
        margin: 0;
    }

    .pricing .card-price .period {
        font-size: 0.8rem;
    }

    .pricing ul li {
        margin-bottom: 1rem;
    }

    .pricing .text-muted {
        opacity: 0.7;
    }

    .pricing .btn {
        font-size: 80%;
        border-radius: 5rem;
        letter-spacing: .1rem;
        font-weight: bold;
        padding: 1rem;
        opacity: 0.7;
        transition: all 0.2s;
    }

    /* Hover Effects on Card */

    @media (min-width: 992px) {
        .pricing .card:hover {
            margin-top: -.25rem;
            margin-bottom: .25rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.3);
        }

        .pricing .card:hover .btn {
            opacity: 1;
        }
    }
</style>