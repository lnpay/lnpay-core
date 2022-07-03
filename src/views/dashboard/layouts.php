<?php $this->beginContent('@app/views/layouts/sidebar/_nav-paywalls.php');?>
    <div class="paywalls-header">
        <h2 style="margin-top: 0px;">My Layouts</h2>
        <a href="/user-layout/create"><button class="styled-button-success">Create Layout <i class="fa fa-plus-circle"></i></button></a>
    </div>

    <div class="layout-content">
        <div class="row">
            <div class="table-responsive">
                <div class="table-responsive layout-content-item">
                    <?php
                        echo $this->render('_layouts',compact('layoutDp'));
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php $this->endContent();?>