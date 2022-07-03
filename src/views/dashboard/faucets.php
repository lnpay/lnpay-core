<?php $this->beginContent('@app/views/layouts/sidebar/_nav-faucets.php');?>
<?php
    $this->title = 'My Faucets';
    $this->params['breadcrumbs'][] = $this->title;
    ?>
    <div class="paywalls-header">
        <h2 style="margin-top: 0px;">My Faucets</h2>
    </div>
    <div class="wallet-content">
        <div class="row">
                <div class="table-responsive">
                    <div class="table-responsive wallet-content-item">
                        <div class="table-responsive">
                            <?php
                                echo $this->render('_faucet-grid',compact('userDp'));
                            ?>
                        </div>
                    </div>
                </div>
        </div>
    </div>
<?php $this->endContent();?>