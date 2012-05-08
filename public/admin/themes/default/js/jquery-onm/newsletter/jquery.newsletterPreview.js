//STEPS
jQuery('#buttons').on('click','#next-button', function() {
    saveChanges();
    jQuery('#newsletterForm').submit();
});

jQuery('#buttons').on('click','#prev-button', function() {

    saveChanges();
    jQuery("#action").val('addContents');
    jQuery('#newsletterForm').submit();

});

jQuery('#newsletterForm').on('click','#edit-button', function() {

    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
    alert('testing as save changes');
});



function saveChanges() {

    //Save subject
    var subject = jQuery('div#content').find('input#subject').val();
    jQuery.cookie("data-subject", JSON.stringify(subject));

    //Save updates

}