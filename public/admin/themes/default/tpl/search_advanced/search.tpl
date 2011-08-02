{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Advanced search{/t}</h2></div>
    </div>
</div>

<div class="wrapper-content">

        <table class="adminheading" style="margin-top:20px; border-top:none !important">
            <tr>
                <th nowrap>{t}Advanced Search{/t}</th>
            </tr>
        </table>
        <table class="adminform" style="text-align:center;">
            <tr>
                <td colspan=2 style="padding:20px; text-align:center" nowrap="nowrap" colspan='3'>
                    <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
                        value="{$smarty.request.stringSearch|escape:"html"|clearslash}"
                        class="required" size="100%" onkeypress="return onSearchKeyEnter(event, this, '_self', 'search', 0);"/> &nbsp;
                    <button type="submit" class="onm-button green" onclick="return enviar(this, '_self', 'search', 0);">{t}Search{/t}</button>
                </td>
            </tr>
            <tr>
                <td colspan=2 style="padding:20px; vertical-align:middle" nowrap="nowrap" colspan='3'>
                    <strong>{t}Content types:{/t}</strong>
                    {$htmlCheckedTypes}
                </td>
            </tr>
        </table>
        <div class="resultsSearch" id="resultsSearch" name="resultsSearch">
            {if !isset($arrayResults) || empty($arrayResults)}
                {include file="search_advanced/partials/_no_results.tpl"}
            {else}
                {include file="search_advanced/partials/_list.tpl"}
            {/if}
        </div>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />

    </form>

</div>
{/block}
