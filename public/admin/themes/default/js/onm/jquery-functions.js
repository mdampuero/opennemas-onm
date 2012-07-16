makeSortable = function(){
    var items =  jQuery( "tbody.sortable" ).sortable().disableSelection();
};


// In video (Fran), dropped this call and will use a custom function
saveSortPositions = function(controller) {
    var items_id = [];

    jQuery( "tbody.sortable tr" ).each(function(){
        items_id.push(jQuery(this).data("id"));
    });
    jQuery.ajax({
       type: "GET",
       url: controller,
       data: {action: "save_positions", positions : items_id },
       success: function( msg ){

           jQuery('#warnings-validation').html("<div class=\"alert\" ><button class=\"close\" data-dismiss=\"alert\">×</button>"+msg+"</div>");

       }
    });
    return false;
};