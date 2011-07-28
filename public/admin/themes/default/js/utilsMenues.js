

makeSortable = function(){
    var lists = $$('ul.elementsContainer');
    var sortables = new Array();
    var drops = new Array();
    lists.each(function(item){
        sortables.push(item.id);
        drops.push(item.id);

    });
   
     drops.push('menuelements');
     sortables.each(function(item){
        Sortable.create( item , {
                tag:'li',
                hoverclass: 'active',
                constraint: 'horizontal',
                containment: drops
        });
     });

        Sortable.create( 'menuelements' , {
                tag:'li',
                dropOnEmpty: true,
                hoverclass: 'active',
                constraint: 'horizontal',
                containment: drops,
                onChange: function(item) {
                    id=item.id;
                     if(!($(id).getAttribute('onclick'))){
                        $(id).setAttribute('onclick', "showActions('"+id+"');");
                     }
                }
        });



}
saveMenu = function() {
    var items = new Array();
    var i=0;
    $$('ul#menuelements li').each( function(item) {
         if(item.getAttribute('id')) {

            items[i] = { 'id':item.getAttribute('id'),  'title': item.getAttribute('title'),
                         'type': item.getAttribute('type'), 'link': item.getAttribute('link'),
                         'pk_item':item.getAttribute('pk_item')
                       };
            i++;
         }
    });

    $('items').value =  Object.toJSON(items);
    //return false;

}

addLink = function() {

	div = $('linkInsertions');
    $('itemTitle').value ='';
    $('link').value ='';
    $('link').removeAttribute('disabled');
    $('saveButton').setAttribute('onclick', "saveLink();");
    div.appear();

}

saveLink = function() {
    ul = $('menuelements');

    var li = document.createElement('li');  
    var name= $('itemTitle').value;
    var link= $('link').value;
    if(name && link) {
        li.setAttribute('class','drag-category');
        li.setAttribute('name', name);
        li.setAttribute('title', name);
        li.setAttribute('id', name);
        li.setAttribute('pk_item','');
        li.setAttribute('type', 'external');
        li.setAttribute('link', link);
        li.setAttribute('onclick', "showActions('"+name+"');");

        li.innerHTML = name;

        ul.appendChild(li);


        new Draggable(name, { revert:true, scroll: window, ghosting:true }  );

        clear();
        $('linkInsertions').hide();
    }
}


editLink = function(id) {

        $('linkInsertions').appear();
        $('itemTitle').value  = $(id).getAttribute('title');
        $('link').value = $(id).getAttribute('link');
        $('saveButton').setAttribute('onclick', "updateLink('"+id+"');");
        if( $(id).getAttribute('type') != 'external') {
            $('link').setAttribute('disabled','true');
        }
}

deleteLink = function(id) {

        var li = $(id);
		if (li) {
			li.parentNode.removeChild(li)
		}
}

updateLink = function(id) {
    var li = $(id);
 
    var name= $('itemTitle').value;
    var link= $('link').value;
    if(name) {
        li.setAttribute('title', name);
    }
    if(link) {
       li.setAttribute('link', link);
    }
    li.innerHTML = name;

    $('linkInsertions').hide();
 
}

clear = function() {
   $('itemTitle').value ='';
   $('link').value ='';
}

hideActions = function() {
     $$('div.div-actions').each(function(item){
        item.setAttribute('style','display:none;');
   });
}

showActions = function(id) {
   hideActions();
   if(!$('actions'+id)){
       createActions(id);
   }
   $('actions'+id).setAttribute('style','position:absolute;top:20px;right:20px;width:80px; ');
   $('actions'+id).observe('mouseout', function() {
           setTimeout('hideActions();', 800);
   });

}

createActions = function(id) {
        var div = document.createElement('div');
        div.setAttribute('class','div-actions');
        div.setAttribute('id', 'actions'+id);
        div.setAttribute('style', 'display:none;');

        div.innerHTML = '<a onclick="editLink(\''+id+'\');">Edit </a> '+
                    ' | <a onclick="deleteLink(\''+id+'\');">Delete </a> ';
 
        $(id).appendChild(div);

}
