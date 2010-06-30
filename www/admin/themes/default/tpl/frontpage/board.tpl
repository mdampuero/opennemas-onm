{toolbar_javascript toolbar="toolbar-top"
    icon="save" click="saveGrid()" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="page-index" text="Cancel"}
    

<style type="text/css" media="all">
div.content-row {
    /*background-color: #EEF;*/
    margin: 0.2em;
}
</style>
<style type="text/css">
    @import url(/themes/{$page->theme|lower}/css/onm-mockup.css)
</style>
    
<div id="menu-acciones-admin">
    <div style="float: left; margin-left: 10px; margin-top: 10px;">
        <h2>{t}Frontpage Manager{/t} - {$page->title} ({$page->theme}/{$page->grid})</h2>
    </div>
    {toolbar name="toolbar-top"}
</div>

<hr class="space" />

{*  *}
<div id="board" class="container">
    {$grid_content}
</div>

{include file="panes/searcher.tpl"}

{if ($request->getActionName() eq "edit")}
<input type="hidden" name="pk_page" id="pk_page" value="{$page->pk_page}" />
<input type="hidden" name="version" id="version" value="{$page->version}" />
{/if}

<script language="Javascript" type="text/javascript">
/* <![CDATA[ */
var saveGrid = function() {    
    
    var data = {
        pk_page: $('#pk_page').val(),
        version: $('#version').val(),
        contents: {}
    };
    
    $('#board div[role=wairole:gridcell]').each(function(i, container) {        
        placeholder = $(container).attr('id');
        
        $(container).find('div.content-row').each(function(weight, content) {
            if(!data.contents[placeholder]) {
                data.contents[placeholder] = [];
            }
            
            obj = {
                'pk_content': $(content).attr('data-pk_content'),
                'mask': $(content).attr('data-mask')
            };
            
            data.contents[placeholder].push(obj);
        });
    });
    
    $.ajax({
        'url': '{baseurl}/{url route="frontpage-savepositions"}',
        'type': 'post',
        'data': data
    });
};

$(document).ready(function() {
    $('#board div[role=wairole:gridcell]').each(function(i, container) {
        $(container).css({
            minHeight: '100px', border: '1px dotted #999', padding: '1em', margin: '0.2em'
        });
        
        /* $(container).hover(
            function() {
                $(this).css({
                    backgroundColor: '#EEF'
                });
            },
            function() {
                $(this).css({
                    backgroundColor: '#F9F9F9'
                });
            }            
        ); */
        
        $(container).sortable({
            items: 'div.content-row',
            placeholder: 'ui-state-highlight',
            dropOnEmpty: true,
            connectWith: '#board div[role=wairole:gridcell]'
        });
        
        $(container).find('select.masks').selectmenu({
            style: 'dropdown',
            change: function(evt) {
                $(this).parents('div.content-row').attr('data-mask', $(this).val());
            },
        });        
    });
});
/* ]]> */
</script>



