saveMenu = function() {
    var items = new Array();
    var i=0;
    $$('ul#menuelements li').each( function(item) {
        if (item.getAttribute('id')) {

            items[i] = {
                'id':item.getAttribute('id'),  'title': item.getAttribute('title'),
                'type': item.getAttribute('type'), 'link': item.getAttribute('link'),
                'pk_item':item.getAttribute('pk_item')
            };
            i++;
        }
    });

    $('items').value =  Object.toJSON(items);

    return false;
};

addFather = function() {
    name = $('father').options[$('father').selectedIndex].getAttribute('name');
};