{toolbar_javascript toolbar="toolbar-top"
    icon="save" click="$.fn.frontmanager.getInstance().saveGrid()" text="Save"}

{toolbar_route toolbar="toolbar-top"
    icon="close" route="page-index" text="Cancel"}
    

<style type="text/css" media="all">
div.content-box {
    /*background-color: #EEF; */
    margin: 0.1em;
}

div.content-box > div.ui-widget-header {
    cursor: move;
}

div.content-box div.ui-widget-header ul {
    cursor: pointer;
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


<script type="text/javascript">
/* <![CDATA[ */
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
/* ]]> */
</script>
