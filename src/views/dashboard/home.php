<?php
use yii\helpers\Html;

$this->title = 'Home';
?>
<?php
 /*echo \yii\bootstrap4\Alert::widget([
    'body' => 'Please see '.Html::a('Webhooks: Getting Started','https://docs.lnpay.co/webhooks/getting-started',['target'=>'_blank']).' for more info',
    'options' => [
        'class' => 'alert-danger',
    ],
]);*/
?>
<div class="container-fluid" id="main">
    <div class="row row-offcanvas row-offcanvas-left">
        <!--/col-->

        <div class="col main">
<!--            <h1 class="display-4 d-none d-sm-block">-->
<!--                LNPay Dashboard-->
<!--            </h1>-->
            <p class="lead d-none d-sm-block">Last 30 days activity</p>

            <div class="row mb-3">
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body bg-success">
                            <div class="rotate">
                                <i class="fa fa-wallet fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Total Wallets</h6>
                            <h1 class="display-4"><?=number_format($walletCount);?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card text-white bg-danger h-100">
                        <div class="card-body bg-danger">
                            <div class="rotate">
                                <i class="fa fa-list fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Transactions (30 days)</h6>
                            <h1 class="display-4"><?=number_format($walletTransactionCount);?></h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card text-white bg-info h-100">
                        <div class="card-body bg-info">
                            <div class="rotate">
                                <i class="fa fa-twitter fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Inbound Volume (30 days)</h6>
                            <h1 class="display-6"><?=number_format($volumeCount['ln_inbound_volume']);?> Sats</h1>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 py-2">
                    <div class="card text-white bg-warning h-100">
                        <div class="card-body">
                            <div class="rotate">
                                <i class="fa fa-share fa-4x"></i>
                            </div>
                            <h6 class="text-uppercase">Outbound Volume (30 days)</h6>
                            <h1 class="display-6"><?=number_format($volumeCount['ln_outbound_volume']);?> Sats</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!--/row-->

            <a id="features"></a>
            <hr>
            <div class="row my-4">
                <div class="col-lg-12 col-md-12">
                    <h3>Successful Transactions</h3>
                    <div class="table-responsive">
                        <?= \yii\grid\GridView::widget([
                            'dataProvider' => $afDpSuccess,
                            'columns' => [
                                'created_at:datetime',
                                [
                                    'header'=>'Type',
                                    'value'=>'walletTransactionType.display_name',
                                ],

                                'num_satoshis',
                            ]
                        ]); ?>
                    </div>
                </div>
                <?php /* ?><div class="col-lg-6 col-md-6">
                    <h3>Failed Payments</h3>
                    <div class="table-responsive">
                        <?= \yii\grid\GridView::widget([
                            'dataProvider' => $afDpFailed,
                            'columns' => [
                                'created_at:datetime',
                                'external_hash',
                                'actionName.display_name',
                                //'actionDataFlat'
                            ]
                        ]); ?>
                    </div>
                </div><?php */?>
            </div>
            <hr>
            <div class="row my-4">
                <div class="col-lg-3 col-md-4">
                    <div class="card">
<!--                        <img class="card-img-top img-fluid" src="//via.placeholder.com/740x180/bbb/fff?text=..." alt="Card image cap">-->
                        <div class="card-body">
                            <h4 class="card-title">Lightning Node</h4>
                            <p class="card-text">Launch a Lightning Node to harness the full power of LNPay</p>
                            <a href="/node/dashboard/add" class="btn btn-primary" >Launch Node</a>
                        </div>
                    </div>
                    <?php /* ?><div class="card card-inverse bg-inverse mt-3">
                        <div class="card-body">
                            <h3 class="card-title">Flexbox</h3>
                            <p class="card-text">Flexbox is now the default, and Bootstrap 4 supports SASS out of the box.</p>
                            <a href="#" class="btn btn-outline-secondary">Outline</a>
                        </div>
                    </div><?php */ ?>
                </div>
                <div class="col-lg-9 col-md-8">
                    <h3>Recent Activity (3 days)</h3>
                    <div class="table-responsive">
                        <?= \yii\grid\GridView::widget([
                            'dataProvider' => $afDp,
                            'columns' => [
                                'created_at:datetime',
                                'external_hash',
                                'actionName.display_name',
                                //'actionDataFlat'
                            ]
                        ]); ?>
                    </div>

                </div>
            </div>
            <!--/row-->
    <?php /* ?>
            <a id="more"></a>
            <hr>
            <h2 class="sub-header mt-5">Account Limits</h2>
            <div class="mb-3">
                <div class="card-deck">
                    <div class="card card-inverse card-success text-center">
                        <div class="card-body">
                            <blockquote class="card-blockquote">
                                <p>It's really good news that the new Bootstrap 4 now has support for CSS 3 flexbox.</p>
                                <footer>Makes flexible layouts <cite title="Source Title">Faster</cite></footer>
                            </blockquote>
                        </div>
                    </div>
                    <div class="card card-inverse card-danger text-center">
                        <div class="card-body">
                            <blockquote class="card-blockquote">
                                <p>The Bootstrap 3.x element that was called "Panel" before, is now called a "Card".</p>
                                <footer>All of this makes more <cite title="Source Title">Sense</cite></footer>
                            </blockquote>
                        </div>
                    </div>
                    <div class="card card-inverse card-warning text-center">
                        <div class="card-body">
                            <blockquote class="card-blockquote">
                                <p>There are also some interesting new text classes for uppercase and capitalize.</p>
                                <footer>These handy utilities make it <cite title="Source Title">Easy</cite></footer>
                            </blockquote>
                        </div>
                    </div>
                    <div class="card card-inverse card-info text-center">
                        <div class="card-body">
                            <blockquote class="card-blockquote">
                                <p>If you want to use cool icons in Bootstrap 4, you'll have to find your own such as Font Awesome or Ionicons.</p>
                                <footer>The Glyphicons are not <cite title="Source Title">Included</cite></footer>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
            <!--/row-->

            <a id="flexbox"></a>
            <hr>
<?php */ ?>
        <!--/main col-->
    </div>

</div>
<!--/.container-->


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Modal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    <span class="sr-only">Close</span>
                </button>
            </div>
            <div class="modal-body">
                <p>This is a dashboard layout for Bootstrap 4. This is an example of the Modal component which you can use to show content.
                    Any content can be placed inside the modal and it can use the Bootstrap grid classes.</p>
                <p>
                    <a href="https://www.codeply.com/go/KrUO8QpyXP" target="_ext">Grab the code at Codeply</a>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary-outline" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>