
/* <![CDATA[ */
var manager = null; // Newsletter.Manager
var searchEngine = null; // Newsletter.SearchEngine

var itemsList = {json_encode value=$items};
var postData  = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

document.observe('dom:loaded', function() {
    var itemsSelected = new Array();
    if(postData!=null && postData.articles) {
        itemsSelected = postData.articles;
    }

    manager = new Newsletter.Manager('items-selected', { items: itemsSelected });

    searchEngine = new Newsletter.SearchEngine('items-list', {
        'items': itemsList,
        'manager': manager,
        'form': 'searchForm'
    });

    $('postmaster').value = Object.toJSON(postData); // Binding post-data

    var botonera = $('buttons').select('ul li a');
    botonera[0].observe('click', function() {
        manager.serialize('articles');

        searchEngine.form.action.value = 'listOpinions';
        searchEngine.form.submit();
    });

    botonera[1].observe('click', function() {
        manager.clearList();
    });

    botonera[2].observe('click', function() {
        searchEngine.selectAll();
    });

    new Newsletter.UISplitPane('container', 'container1', 'container2', 'separator');

    // Wizard icons step
    $('map').select('area').each(function(tagArea) {
        tagArea.observe('click', function(evt) {
            Event.stop(evt);

            var attr = this.getAttribute('action');

            var form = $('searchForm');
            manager.serialize('articles'); // global object

            form.action.value = attr;
            form.submit();
        });
    });
});
/* ]]> */
