$(document).ready(function(){
    if($(window).width() < 768){
        $('.navbar-toggler').click(function(){
            openNav();
        });
        function openNav() {
            document.getElementById("myNav").style.height = "100%";
            var togle=false;
            $('body').css({'height':'100%','overflow':'hidden'});
        }
        $('.closebtn').click(function(){
            document.getElementById("myNav").style.height = "0%";
            $('body').css({'height':'auto','overflow':'visible'});
        });
    }
    $('.ajaxFormLoader').on('ajaxBeforeSend', function (event, jqXHR, settings) {
        // Activate waiting label
        addLoadingCssToButton($(".ajaxFormLoader button"));
    }).on('ajaxComplete', function (event, jqXHR, textStatus) {
        // Deactivate waiting label
        var response = jQuery.parseJSON(jqXHR.responseText);
        var hasErrors = Object.keys(response).length;
        if (hasErrors>0)
            removeLoadingCssFromButton($(".ajaxFormLoader button"));
    });
});

function addLoadingCssToButton(button) {
    button.prop("disabled",true);
    button.append('<span class="cssLoader fa fa-spinner fa-spin fa-fw margin-bottom"></span>');
    button.addClass('addedLoadingCssToButton');
}
function removeLoadingCssFromButton(button) {
    $(".cssLoader").removeClass("cssLoader fa fa-spinner fa-spin fa-fw margin-bottom");
    button.removeClass('disabled');
    button.prop('disabled',false);
}

function copyTextToClipboard(elementId) {
    /* Get the text field */
    var copyText = document.getElementById(elementId);

    /* Select the text field */
    copyText.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    //alert("Copied the text: " + copyText.value);
}

function CopyTextFromElementToClipboard(containerid) {
    if (document.selection) {
        var range = document.body.createTextRange();
        range.moveToElementText(document.getElementById(containerid));
        range.select().createTextRange();
        document.execCommand("copy");

    } else if (window.getSelection) {
        var range = document.createRange();
        range.selectNode(document.getElementById(containerid));
        window.getSelection().addRange(range);
        document.execCommand("copy");
        //alert("text copied, copy in the text-area")
    }
}

