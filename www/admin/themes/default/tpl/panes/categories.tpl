{* panes/categories.tpl *}

<fieldset id="pane-categories">
    
    {if isset($legend)}<legend>{$legend}</legend>{/if}
    
    <dl>
        <dt></dt>
        <dd>
            {category_multiselect id="categories" selected=$selected}
        </dd>        
    </dl>
</fieldset>

