{toolbar_javascript toolbar="toolbar-top"
    icon="save" click="$.fn.frontmanager.getInstance()" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="page-index" text="Cancel"}
    

<style type="text/css" media="all">
div.content-box {
    /*background-color: #EEF; */
    margin: 0.1em;
}

div.content-box div.ui-widget-header ul {
    list-style: none;
    float: right;
}

div.content-box div.ui-widget-header ul li {
    float: left;
    margin-right: 0.2em;
}

div.content-box div.ui-widget-header .content-box-masks {
    background-color: white;
    border: 1px solid #CCC;
    
    height: 200px;
    width: 150px;
    overflow: auto;
    
    z-index: 3333;
    position: absolute;
    top: 0;
    left: 0;
    display: none;
}

div.content-box div.ui-widget-header .content-box-masks li {
    float: left;
}

div#board {
    padding: 0 !important;
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

<div id="board" class="container">
    {$grid_content}
</div>

{include file="panes/searcher.tpl"}

{if ($request->getActionName() eq "edit")}
<input type="hidden" name="pk_page" id="pk_page" value="{$page->pk_page}" />
<input type="hidden" name="version" id="version" value="{$page->version}" />
{/if}

{* <script language="Javascript" type="text/javascript">
/* <![CDATA[ */
{literal}
var templateContentBox = '<div class="ui-widget content-box" data-pk_content="${pk_content}" data-mask="${mask}"> \
    <div class="ui-widget-header ui-corner-top ui-helper-clearfix"> \
        <span>${title}</span> \
        <ul class="ui-helper-reset"> \
            <li><span data-action="toggle" class="ui-icon ui-icon-minus"></span></li> \
            <li><span data-action="modify" class="ui-icon ui-icon-newwin"></span></li> \
            <li><span data-action="drop" class="ui-icon ui-icon-circle-close"></span></li> \
        </ul> \
    </div> \
    <div class="ui-widget-content ui-corner-bottom"> \
        <div class="clearfix"><img src="/admin/images/loading.gif" border="0" /> Rendering content...</div> \
    </div>\
</div>';
{/literal}

var handlerButtons = function(event) {
    event.preventDefault();
    event.stopPropagation();
    
    var action = $(event.target).attr('data-action');
    switch(action) {
        case 'toggle':
            if($(event.target).hasClass('ui-icon-minus')) {
                $(event.target).removeClass('ui-icon-minus')
                    .addClass('ui-icon-plus')
                    .parents("div.ui-widget")
                    .find('div.ui-widget-content')
                    .hide();
            } else {                
                $(event.target).removeClass('ui-icon-plus')
                    .addClass('ui-icon-minus')
                    .parents("div.ui-widget")
                    .find('div.ui-widget-content')
                    .show();
            }
        break;
        
        case 'drop':                
            if(confirm('{t}Are you sure?{/t}')) {
                var deletable = $(event.target).parents("div.ui-widget").remove();
            }
        break;
        
        case 'repaint':            
            if($(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css('display') == 'none') {
                pos = $(event.target).position();
                $(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css({
                    top: (parseInt(pos.top) + 16) + 'px',
                    left: (parseInt(pos.left) - 134) + 'px',
                    display: 'block'
                });
            } else {
                $(event.target).parents("div.ui-widget-header").find('div.content-box-masks').css({
                    display: 'none'
                });
            }
        break;
    }    
};

var saveGrid = function() {    
    
    var data = {
        pk_page: $('#pk_page').val(),
        version: $('#version').val(),
        contents: {}
    };
    
    $('#board div[role=wairole:gridcell]').each(function(i, container) {        
        placeholder = $(container).attr('id');
        
        $(container).find('div.content-box').each(function(weight, content) {
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
            minHeight: '100px', border: '1px dotted #999', padding: '0.2em', margin: '0.2em'
        });
        
        $(container).sortable({
            items: 'div.content-box',
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            dropOnEmpty: true,
            connectWith: '#board div[role=wairole:gridcell], #searcher-results',
            receive: function(event, ui) {                                
                if(ui.item.hasClass('searcher-item')) {
                    var pk_content = ui.item.attr('data-pk_content');                    
                    var newContentBox = $.template(templateContentBox).apply({
                        title: ui.item.text(),
                        pk_content: pk_content,
                        mask: ''
                    });
                    
                    var context = $(newContentBox).replaceAll(ui.item);
                    context.find('div.ui-widget-header>ul>li>span').click(handlerButtons);
                    
                    $.ajax({
                        'url': '{baseurl}/{url route="frontpage-repaint"}?pk_page=' + $('#pk_page').val() +
                             '&pk_content=' + pk_content,
                        'success': function(data) {
                            $(this).find('div.ui-widget-content>div.clearfix').html(data);
                        },
                        'context': context
                    });
                }
            }
        });       
    });
    
    $('#board div.ui-widget-header>ul>li>span').click(handlerButtons);        
});
/* ]]> */
</script> *}

<script type="text/javascript">
jQuery(document).ready(function() {
    $('#board').frontmanager({
        'savePosURI':  '{baseurl}/{url route="frontpage-savepositions"}',
        'repaintURI':  '{baseurl}/{url route="frontpage-repaint"}',
        'getMasksURI': '{baseurl}/{url route="frontpage-getmasks"}',
        'pk_page':     $('#pk_page').val(),
        'version':     $('#version').val(),
        'confirmText': '{t}Are you sure?{/t}',
        'waitingText': '{t}Rendering content...{/t}',
        'connectWith': 'div[role=wairole:gridcell], #searcher-results'
    });
});
</script>



