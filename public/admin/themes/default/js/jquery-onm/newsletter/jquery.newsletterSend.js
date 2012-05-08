
jQuery('#buttons').on('click','#prev-button', function() {

    jQuery("#action").val('preview');
    jQuery('#newsletterForm').submit();

});