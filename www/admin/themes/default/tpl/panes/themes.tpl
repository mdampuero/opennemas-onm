{* *****************************************************************************
 * {include file="panes/themes.tpl" value=$page legend="Themes" themes=$themes grids=$grids}
 * Pane name:
 *    panes/themes.tpl
 *    
 * Params:
 *    $value
 *    $themes
 *    $grids
 *    $legend (optional)
 ***************************************************************************** *}

<style type="text/css">
{section name=t loop=$themes}
li.page-theme-{$themes[t].name} > a {ldelim}
    background-image: url({$smarty.const.SITE_PATH_WEB}themes/{$themes[t].name}/{$themes[t].info.thumbnail});
    background-repeat: no-repeat;
    background-position: left center;
    font-size: 18px;
    padding: 2px 0;
    padding-left: 60px;
    height: 48px;    
{rdelim}
{/section}
</style>

<fieldset id="pane-themes" class="{$className}">
    
    {if isset($legend)}<legend>{t}{$legend}{/t}</legend>{/if}
    
    <dl>
        <dt>
            <label for="theme">{t}Themes{/t}:</label>
        </dt>        
        <dd>
            <select name="theme" id="theme">
                {section name=t loop=$themes}
                <option value="{$themes[t].name}"
                        class="page-theme-{$themes[t].name}" 
                        {if $value->theme == $themes[t].name}selected="selected"{/if}>
                    {$themes[t].info.name}
                </option>
                {/section}
            </select>
        </dd>
        
        <dt>
            <label for="grid">{t}Grids{/t}:</label>
        </dt>
        <dd>
            <select name="grid" id="grid"></select>
        </dd>
    </dl>
</fieldset>


<script type="text/javascript">
/* <![CDATA[ */
var grids = {json_encode value=$grids};

{literal}
reloadGridOptions = function(themeSelected, gridSelected) {
    // Remove selectmenu jquery widget
    $('#grid-menu, #grid-button').each(function(item, elem) {
        $(elem).remove();
    });
    
    var slt = $('select#grid').get(0);
    var flag = false;
    slt.options.length = 0;
    for(var i in grids[themeSelected]) {
        flag = (grids[themeSelected][i] == gridSelected);
        slt.options[slt.options.length] = new Option(grids[themeSelected][i], grids[themeSelected][i], flag, flag);
    }
    
    // Create selectmenu jquery widget
    $('select#grid').selectmenu({
        style: 'dropdown',
        width: 200
    });
};

$(function() {
    $('select#theme').selectmenu({
        style: 'dropdown',
        width: 200,
        change: function(evt) {
            reloadGridOptions($(this).val(), null);
        },
        format: function(param) {            
            return param;
        }
    });
    
    var selected = $('select#theme').val();    
    reloadGridOptions(selected, {/literal}'{$value->grid}'{literal});
});
{/literal}
/* ]]> */
</script>
