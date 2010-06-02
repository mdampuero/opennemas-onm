{* *****************************************************************************
 * {include file="panes/categories.tpl" selected=$object legend="Information"}
 * Pane name:
 *    panes/categories.tpl
 *    
 * Params:
 *    $selected     Array of selected categories
 *    $legend (optional)
 ***************************************************************************** *}

<fieldset id="pane-categories" class="{$className}">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt></dt>
        <dd>
            {category_multiselect id="categories" selected=$selected}
        </dd>        
    </dl>
</fieldset>