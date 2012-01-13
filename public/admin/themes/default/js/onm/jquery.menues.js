/*
* jquery functions
* Management menu: drag-drop items, delete item, add item.
*/

jQuery(document).ready(function(){

    makeSortable = function(){


        jQuery( 'ul.elementsContainer' ).sortable({
            connectWith: ".menuelements",
            placeholder: 'placeholder-element'
        }).disableSelection();

        jQuery( '.menuelements' ).sortable({
            connectWith: 'ul.elementsContainer',
            placeholder: 'placeholder-element'
        }).disableSelection();

    }();

});



 /*

//[{"id": "item_90", "title": "frontpage", "type": "internal", "link": "", "pk_item": "90"},
//   {"id": "item_91", "title": "Galicia", "type": "category", "link": "galicia", "pk_item": "91"}]
saveMenu = function() {
    var items = new Array();

    jQuery('ul.menuelements li').each( function() {

          if( item.id ) {

              items[i] =  { "id": $(this).id,
                           "title": item.title,
                           "type": item.type,
                           "link": item.link,
                           "pk_item": item.pk_item,
                         };

         }

    });

      var val = jQuery.toJSON(items);

   jQuery('input#items').attr('value', items );

    return false;

}
*/

addLink = function() {

    jQuery('#itemTitle').attr('value','');
    jQuery('#link').attr('value','');
    jQuery('#link').removeAttr('disabled');
    jQuery('#saveButton').attr('onclick', 'saveLink();');

    jQuery('#linkInsertions').show('blind');

    return false;
}

hideDiv = function() {
    jQuery('#linkInsertions').hide();
}

clear = function() {
   jQuery('#itemTitle').attr('value','');
   jQuery('#link').attr('value','');
}

saveLink = function() {

    var name = jQuery('#itemTitle').attr('value');
    var link = jQuery('#link').attr('value');

    if(name && link) {
        ul = jQuery('#menuelements');

        var li = document.createElement('li');

        ul.append( '<li title="'+ name +'" link="'+ link +
                    '" class="menuItem" name="'+ name +'" id ="'+ name +
                    '" pk_item="" type="external">'+name+'</li>' );


        clear();
        jQuery('#linkInsertions').hide();
    }
}

editLink = function(id) {

        jQuery('#linkInsertions').show(('blind'));
        var title = jQuery('#'+id).attr('title');
        var link = jQuery('#'+id).attr('link');
        jQuery('#itemTitle').attr('value', title );
        jQuery('#IdItem').attr('value', id );
        jQuery('#link').attr('value', link );
        jQuery('#saveButton').attr('id', "updateButton");
        if( jQuery('#'+id).attr('type') != 'external') {
             jQuery('#link').attr('disabled','true');
        }

}


deleteLink = function(id) {

        var deletes = jQuery('#forDelete').attr('value') + ", "+ jQuery('#'+id).attr('pk_item');
        jQuery('#forDelete').attr('value', deletes);

        jQuery('#'+id).remove();
}

updateLink = function() {
    var id = jQuery('#IdItem').attr('value');
    var name= jQuery('#itemTitle').attr('value');
    var link= jQuery('#link').attr('value');

    if(name) {
        jQuery('#'+id).attr('title', name);
    }
    if(link) {
       jQuery('#'+id).attr('link', link);
    }

    jQuery('#'+id).text(name);

    jQuery('#linkInsertions').hide();

}

hideActions = function() {
   jQuery('.menuItem').attr( 'style','background:white');
   jQuery('div.div-actions').remove();

}
createActions = function(id) {
    jQuery('#'+id).append('<div class="div-actions" style="margin-top:10px; width: 60px;">'+
    '<a onclick="editLink(\''+id+'\');">Edit </a>  | <a onclick="deleteLink(\''+id+'\');">Delete </a> </div>');

}

showActions = function(id) {

    jQuery('#'+id).attr( 'style','background:#eee');

   hideActions();

   createActions(id);

}


//Handle events

jQuery('#saveButton').bind( {
    click: function() {

        if(this.id == 'updateButton') {
             updateLink();
        }else{
             saveLink();
        }
    },
});


jQuery('#updateButton').bind( {
    click: function() {
            updateLink();
    },
});

jQuery('li.menuItem').bind( {
    dblclick: function( ) {
        showActions( this.id );
    },

});

jQuery('li.menuItem').bind('mouseout', function() {
   setTimeout('hideActions();', 800);
});

jQuery('.menuelements').bind( "sortreceive", function(event, ui) {

       jQuery(ui.item).attr('class','menuItem');
       var id =  jQuery(ui.item).attr('id');

       jQuery(ui.item).attr('ondblclick', ' showActions( "'+ id +'" )' );


});