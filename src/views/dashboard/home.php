<?php
use yii\helpers\Html;

$this->title = 'Home';
?>
<h1>Services</h1>
    <div class="row">
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail text-center">
                <div class="caption">
                    <img src="https://cloud.lnpay.co/img/icon_large_wallets.png" style="height: 100px" />
                    <h3>Wallets</h3>
                    <p>Create, manage, send, receive from fully functional wallets to segment your funds across services. </p>
                    <p><a href="/wallet/wallet/dashboard" class="btn btn-primary" role="button">Go to Wallets <i class="fa fa-arrow-circle-right"></i></a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail text-center">
                <div class="caption">
                    <img src="https://cloud.lnpay.co/img/icon_large_nodes.png" style="height: 100px" />
                    <h3>Lightning Node</h3>
                    <p>Manage the Lightning Node powering your wallets.</p>
                    <p><a href="/node/dashboard/index" class="btn btn-primary" role="button">Go to Lightning Node <i class="fa fa-arrow-circle-right"></i></a></p>
                </div>
            </div>
        </div>
    </div>
    <h1>Developers</h1>
    <div class="row">
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail text-center">
                <div class="caption">
                    <img src="/img/icons/flowchart.svg" style="height: 100px" />
                    <h3>Developer Dashboard</h3>
                    <p>API keys and general integration info </p>
                    <p><a href="/developers/dashboard" class="btn btn-primary" role="button">Go to Developer Dash <i class="fa fa-arrow-circle-right"></i></a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail text-center">
                <div class="caption">
                    <img src="/img/icons/webhook.svg" style="height: 100px" />
                    <h3>Webhooks</h3>
                    <p>Create webhooks to your server for important events - paywall paid, faucet pull, wallet receive, etc.</p>
                    <p><a href="/developers/webhook" class="btn btn-primary" role="button">Go to Webhooks <i class="fa fa-arrow-circle-right"></i></a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail text-center">
                <div class="caption">
                    <img src="/img/icons/log.svg" style="height: 100px" />
                    <h3>API Logs</h3>
                    <p>View and debug logs for development purposes </p>
                    <p><a href="/developers/api-log" class="btn btn-primary" role="button">Get Started <i class="fa fa-arrow-circle-right"></i></a></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="thumbnail text-center">
                <div class="caption">
                    <h3>API Documentation</h3>
                    <p>Check out the API documentation on how to use wallets for your service or as a tool.</p>
                    <p><a href="https://docs.lnpay.co" class="btn btn-primary" role="button" target="_blank">Got to API Docs <i class="fa fa-external-link-alt"></i></a></p>
                </div>
            </div>
        </div>

    </div>
