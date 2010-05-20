{* panes/info.tpl *}

<fieldset id="pane-info">
    
    {if isset($legend)}<legend>{$legend}</legend>{/if}
    
    <dl>
        <dt>
            <label>{t}Created{/t}</label>
        </dt>
        <dd>
            {$content->created}
        </dd>
        
        <dt>
            <label>{t}Changed{/t}</label>
        </dt>
        <dd>
            {$content->changed}
        </dd>
        
        <dt>
            <label>{t}Author{/t}</label>
        </dt>
        <dd>
            {$content->fk_author}
        </dd>
        
        <dt>
            <label>{t}Publisher{/t}</label>
        </dt>
        <dd>
            {$content->fk_publisher}
        </dd>
        
        <dt>
            <label>{t}Last editor{/t}</label>
        </dt>
        <dd>
            {$content->fk_user_last_editor}
        </dd>
    </dl>
</fieldset>