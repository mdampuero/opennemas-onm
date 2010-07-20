{* *****************************************************************************
 * {include file="panes/innerparams.tpl"
 *          content=$content legend="Information"}
 * Pane name:
 *    panes/innerparams.tpl
 *    
 * Params:
 *    $params     Array of params content
 *    $legend (optional)
 ***************************************************************************** *}

<fieldset id="pane-innerparams" class="{$className}">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt><label for="params-innerpage">Inner page</label></dt>
        <dd>            
            {assign var="innerpage" value=$content->getParam('innerpage')}
            
            <select name="params[innerpage]" id="params-innerpage">                
                {innerpage_select selected=$innerpage}
            </select>
        </dd>        
    </dl>
    
    {* <dl>
        <dt><label for="params-innermask">Inner mask</label></dt>
        <dd>
            <select name="params[innermask]" id="params-innermask">
                
            </select>
        </dd>        
    </dl> *}
</fieldset>