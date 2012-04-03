/*
* jquery functions
* Get specials contents for save in database.
*/
saveSpecialContent = function() {
    var itemsLeft = [];
    var itemsRight= [];

    jQuery( "div#cates div#column_right ul.content-receiver li" ).each(function(){
        itemsRight.push(jQuery(this).data("id"));
    });
    jQuery( "div#cates div#column_left ul.content-receiver li" ).each(function(){
        itemsLeft.push(jQuery(this).data("id"));
    });
    //test JSON.stringify
    jQuery('input#noticias_right').val(itemsRight.join(','));
    jQuery('input#noticias_left').val(itemsLeft.join(','));

}

/*
 * Toggle divs with specials content.
 */
setOnlyPdf = function(my){
    jQuery(my).click(function(){
        jQuery('div.special-container').toggle();
	});
}
