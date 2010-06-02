{* *****************************************************************************
 * {pane_info value=$content}
 * 
 * {include file="panes/info.tpl" value=$value legend="Information"}
 * 
 * Pane name:
 *    panes/info.tpl
 *    
 * Params:
 *    $value
 *    $legend (optional)
 ***************************************************************************** *}

<fieldset id="pane-info">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt>
            <label>{t}Created{/t}</label>
        </dt>
        <dd>
            {$value->created|date_format:"%d/%m/%Y %H:%M:%S"}
        </dd>
        
        <dt>
            <label>{t}Changed{/t}</label>
        </dt>
        <dd>
            {$value->changed|date_format:"%d/%m/%Y %H:%M:%S"}
        </dd>
        
        <dt>
            <label>{t}Author{/t}</label>
        </dt>
        <dd>
            {$value->fk_author|user_login}
        </dd>
        
        <dt>
            <label>{t}Publisher{/t}</label>
        </dt>
        <dd>
            {$value->fk_publisher|user_login}
        </dd>
        
        <dt>
            <label>{t}Last editor{/t}</label>
        </dt>
        <dd>
            {$value->fk_user_last_editor|user_login}
        </dd>
    </dl>
</fieldset>
