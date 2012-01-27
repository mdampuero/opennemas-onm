{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css" media="screen">
#stringSearch {
    width:80%;
}
label {
    font-weight:normal;
}
.content-type-picker {
    display:inline-block;
    padding:3px 0;
    min-width:90px;
    margin-right:5px;
}
</style>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Advanced search{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

        <table class="adminheading">
            <tr>
                <th>{t}Advanced Search{/t}</th>
            </tr>
        </table>
        <table class="adminform">

            <tr>
                <td style="padding:20px; text-align:center">
                    <input type="text" id="stringSearch" name="stringSearch" title="stringSearch" value="{$search_string}"
                            class="required" onkeypress="return onSearchKeyEnter(event, this, '_self', 'search', 0);"/> &nbsp;
                    <button type="submit" class="onm-button green" onclick="return enviar(this, '_self', 'search', 0);">{t}Search{/t}</button>
                </td>
            </tr>
            <tr>
                <td style="padding:20px; padding-top:0; vertical-align:middle; text-align:left">
                {foreach name=contentTypes item=type from=$arrayTypes}
                    <div class="content-type-picker">
                        {if $type[0] == 1 || $type[0] == 4}
                        <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" checked="checked"/>
                        <label for="{$type[1]}">{$type[2]|mb_capitalize}</label>
                        {else}
                        <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" />
                        <label for="{$type[1]}">{$type[2]|mb_capitalize}</label>
                        {/if}
                    </div>
                {/foreach}
                </td>
            </tr>
        </table>
        {if isset($search_string)}
        <div class="resultsSearch" id="resultsSearch" name="resultsSearch">
            {if !isset($arrayResults) || empty($arrayResults)}
                {include file="search_advanced/partials/_no_results.tpl"}
            {else}
                {include file="search_advanced/partials/_list.tpl"}
            {/if}
        </div>
        {/if}
    </form>
</div>
{/block}
