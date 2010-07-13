{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Widget Manager{/t}{/block}

{block name="body-content"}
    <form action="{baseurl}/widget/{$request->getActionName()}" method="post">

    {flashmessenger}
    
    {toolbar_button toolbar="toolbar-top"
        icon="apply" type="submit" text="Apply"}
    
    {toolbar_route toolbar="toolbar-top"
        icon="close" route="widget-index" text="Cancel"}    
        
    <div id="menu-acciones-admin">
        <div style="float: left; margin-left: 10px; margin-top: 10px;">
            <h2>{t}Widget Manager{/t}</h2>
        </div>
        {toolbar name="toolbar-top"}
    </div>
    
    <h2>{t}Delete{/t} <strong>{$widget->title}</strong>.{t}Are you sure?{/t}</h2>
    
    <input type="hidden" name="id" value="{$request->getParam('id')}" />
    
    </form>
{/block}