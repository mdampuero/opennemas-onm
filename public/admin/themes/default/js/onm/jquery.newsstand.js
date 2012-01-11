
makeSortable = function(){

    var items =  jQuery( "tbody.sortable" ).sortable().disableSelection();


}


saveSortPositions = function() {
    var items_id = [];
    jQuery( "tbody.sortable tr" ).each(function(){
        items_id.push(jQuery(this).data("id"));
    })

    jQuery.ajax({
       type: "GET",
       url: "newsstand.php?action=save_positions&",
       data: {positions : items_id },
       success: function( msg ){

           jQuery('#warnings-validation').html("<div class=\"success\">"+msg+"</div>")
                                         .effect("highlight", {}, 3000);

       }
    });



}
 