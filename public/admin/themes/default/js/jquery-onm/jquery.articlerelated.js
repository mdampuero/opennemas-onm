 $('#button_savepositions').on('click',function(e, ui) {
        e.preventDefault();
        var els = get_contents_in_frontpage();
        //JSON.stringify(get_contents_in_frontpage());
        var category = $("#frontpagemanager").data("category");


    });



function get_related_contents() {
    var els = [];

    jQuery('div.placeholder').each(function (){
        var placeholder = jQuery(this).data('placeholder');
        jQuery(this).find('div.content-provider-element').each(function (index){
            els.push({
                'id' : jQuery(this).data('content-id'),
                'content_type': jQuery(this).data('class'),
                'placeholder': placeholder,
                'position': index,
                'params': {}
            });
        });

    });
    return els;
}
