<?php /** @noinspection ALL */

/* @var $this yii\web\View */

$this->registerJs('
$("#withdraw-loader").hide();
$(\'#withdrawForm\').on(\'ajaxBeforeSend\', function (event, jqXHR, settings) {
    // Activate waiting label
    $("#withdraw-loader").show();
}).on(\'ajaxComplete\', function (event, jqXHR, textStatus) {
    // Deactivate waiting label
    $("#withdraw-loader").hide();
});
');

$this->registerJs("$('.sidebar a[href^=\"' + location.pathname + '\"').addClass('active');");
?>