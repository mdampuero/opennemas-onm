makeSortable = function(){

    var items =  jQuery( "tbody.sortable" ).sortable().disableSelection();


}


saveSortPositions = function( controller) {
    var items_id = [];
    jQuery( "tbody.sortable tr" ).each(function(){
        items_id.push(jQuery(this).data("id"));
    })

    jQuery.ajax({
       type: "GET",
       url: controller,
       data: {action: "save_positions", positions : items_id },
       success: function( msg ){

           jQuery('#warnings-validation').html("<div class=\"success\">"+msg+"</div>")
                                         .effect("highlight", {}, 3000);

       }
    });



}