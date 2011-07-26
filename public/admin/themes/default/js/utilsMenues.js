 
saveMenu = function() {
    var items = new Array();
    var i=0;
    $$('ul#menu-categories li').each( function(item) {
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

	div = $('divInsert');
  
 
	tItem = document.createElement('label');
	tItem = document.createTextNode('Title: ');

	dItem = document.createElement('input');
	dItem.setAttribute('name','itemTitle');
	dItem.setAttribute('value','');
	dItem.setAttribute('type','text');
	dItem.setAttribute('id','itemTitle');
	dItem.setAttribute('size','60');

 

    pItem = document.createElement('label');
	pItem = document.createTextNode('Link: ');
    lItem = document.createElement('input');
	lItem.setAttribute('name','link');
	lItem.setAttribute('value','');
	lItem.setAttribute('type','text');
	lItem.setAttribute('id','link');
	lItem.setAttribute('size','60');



	a = document.createElement('a');
	a.title='Save';
    a.innerHTML ='Save';
	var funcion='saveLink()';
	a.setAttribute('onclick', funcion);
    a.setAttribute('style', 'cursor:pointer');


	
    div.appendChild(tItem);
    div.appendChild(dItem);
    div.appendChild(pItem);
    div.appendChild(lItem);
    div.appendChild(a);


}


saveLink = function() {
    ul = $('menu-categories');

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

    Sortable.create('menu-categories', {
                                        tag:'li',
                                        dropOnEmpty: true,
                                        containment:[ 'ul-categories', 'menu-categories' ]
                                       });

    div.innerHTML = '';
 
}