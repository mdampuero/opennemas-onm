{toolbar_route toolbar="toolbar-top"
    icon="new" route="page-create" text="New Page"}

{toolbar_javascript toolbar="toolbar-top"
    icon="alacarte" click="saveOrder()" text="Save order"}
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Page Manager{/t}</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<table class="adminheading">
    <tbody>
        <tr>
            <th>{t}Pages{/t}</th>
        </tr>
    </tbody>
</table>

<div id="pagetree">
    {$list}
</div>

<script type="text/javascript">
/* <![CDATA[ */
$('#pagetree>ul ul').sortable({
    placeholder: 'ui-state-highlight',
    forcePlaceholderSize: true,
    update: function(event, ui) {
        $('div#messageboard').html('<ul class="flashmessenger"><li class="warning">{t}Remember save the relocation of pages{/t}</li></ul>');
    }
});

var saveOrder = function() {
    var dataOrder = {
        'pages-pk': [],
        'pages-weight': []
    };
    
    $('div#pagetree ul').each(function(i, list){
        $(list).find('> li').each(function(j, node) {
            dataOrder['pages-pk'].push( node.getAttribute('id').replace(/page\-(\d+)/, '$1') ); // pk_page
            dataOrder['pages-weight'].push(j); // weight
        });
    });
    
    $('div#messageboard').html('<ul class="flashmessenger"><li class="warning">{t}Wait please! saving page relocation ...{/t}</li></ul>');
    
    $.ajax({
        url: '{baseurl}/{url route="page-relocate"}',
        data: dataOrder,
        type: 'POST',
        success: function(response, status) {
            $('div#messageboard').html('<ul class="flashmessenger"><li class="notice">{t}Tree of pages relocated successfully{/t}</li></ul>');
        },
        error: function() {
            $('div#messageboard').html('<ul class="flashmessenger"><li class="error">{t}Ups! Server has a problem. Try it again{/t}</li></ul>');
        }
    });
};
/* ]]> */
</script>
