<?php $this->beginContent('@app/views/layouts/sidebar/_nav-paywalls.php');?>
<?php
    $this->title = 'My Paywalls';
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="paywalls-header">
    <h2 style="margin-top: 0px;">My Paywalls</h2>
    <a href="/link/create"><button class="styled-button-success">Create Paywall <i class="fa fa-plus-circle"></i></button></a>
    </div>
    <div class="wallet-content">
        <div class="row">
          <?php if (\LNPay::$app->user->isGuest) {?>

              <div class="col-md-12 text-center">
                  <?=Html::a('Register / Sign in to access',['/home/signup'],['class'=>'btn btn-info btn-lg']);?>
              </div>
          <?php } else { ?>
          <div class="table-responsive">
              <div class="table-responsive wallet-content-item">
                  <?php
                      echo $this->render('_link-totals',compact('userDp'));
                  ?>
              </div>
          </div>
          <?php } ?>
        </div>
    </div>
<?php $this->endContent();?>