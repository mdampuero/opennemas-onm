//STEPS
jQuery('#buttons').on('click','#next-button', function() {
    jQuery('#newsletterForm').submit();
});

jQuery('#buttons').on('click','#prev-button', function() {

    jQuery("#action").val('addContents');
    jQuery('#newsletterForm').submit();

});

jQuery('#savedNewsletter').on('click','#edit-button', function() {

    alert('testing');
});
