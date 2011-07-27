 
saveMenu = function() {
    var items = new Array();
    var i=0;
    $$('ul#menuelements li').each( function(item) {
         if(item.getAttribute('id')) {

            items[i] = { 'id':item.getAttribute('id'), 'title': item.getAttribute('title'),
                         'type': item.getAttribute('type'), 'link': item.getAttribute('link')
                       };
            i++;
         }
    });

    $('items').value =  Object.toJSON(items);
    //return false;

}


addLink = function() {

	div = $('linkInsertions');
    div.appear();

}


saveLink = function() {
    ul = $('menuelements');

    var li = document.createElement('li');  
    var name= $('itemTitle').value;
    var link= $('link').value;

    li.setAttribute('class','drag-category');
    li.setAttribute('name', name);
    li.setAttribute('title', name);
    li.setAttribute('id', name);
    li.setAttribute('type', 'external');
    li.setAttribute('link', link);

    li.innerHTML = name;

    ul.appendChild(li);

    Sortable.create('menuelements', {
                                        tag:'li',
                                        dropOnEmpty: true,
                                        containment:[ 'availablecategories', 'menuelements' ]
                                       });

    clear();
 
}

clear = function() {
   $('itemTitle').value ='';
   $('link').value ='';
}