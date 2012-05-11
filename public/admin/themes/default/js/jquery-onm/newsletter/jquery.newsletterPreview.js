//STEPS
jQuery('#buttons').on('click','#next-button', function() {
    saveChanges();
   // jQuery('#newsletterForm').submit();
});

jQuery('#buttons').on('click','#prev-button', function() {

    saveChanges();
    alert('Si vuelve atrás perderá los cambios realizados');
    jQuery("#action").val('updateContents');
    jQuery('#newsletterForm').submit();

});

jQuery('#newsletterForm').on('click','#edit-button', function() {

    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );

});

jQuery('#newsletterForm').on('click','#save-button', function() {
    saveChanges();
});




function saveChanges() {

    //Save subject
    var subject = jQuery('div#content').find('input#subject').val();
    jQuery.cookie("data-subject", JSON.stringify(subject));

    //Save updates
    if(tinyMCE.get('htmlContent')) {
        OpenNeMas.tinyMceFunctions.saveTiny( 'htmlContent' )
        OpenNeMas.tinyMceFunctions.destroy( 'htmlContent' );
    }
    var htmlContent = jQuery('div#content').find('div#htmlContent').html();
    jQuery.ajax({
        url:  "/admin/controllers/newsletter/newsletter.php",
        type: "POST",
        data: { action:"saveNewsletterContent", html:htmlContent },
    });


}