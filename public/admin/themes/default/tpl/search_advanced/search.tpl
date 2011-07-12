{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

        <div id="menu-acciones-admin" class="clearfix">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}</h2></div>
            <ul>
                <li>
                    <a href="#" class="admin_add" onclick="enviar(this, '_self', 'search', 0);" onmouseover="return escape('<u>S</u>earch');" accesskey="N" tabindex="1" title="Search">
                        <img border="0" src="{$params.IMAGE_DIR}search.png" title="Search" alt="Search"><br />Search
                    </a>
                </li>
            </ul>
        </div>

        <table class="adminheading" style="margin-top:20px; border-top:none !important">
            <tr>
                <th nowrap>{t}Advanced Search{/t}</th>
            </tr>
        </table>
        <table class="adminlist" style="text-align:center;">
            <tr>
                <td colspan=2 style="padding:20px;" nowrap="nowrap" colspan='3'>
                    <label for="title" >{t}Search string:{/t}</label>
                    <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
                        value="{$smarty.request.stringSearch|escape:"html"|clearslash}"
                        class="required" size="100%" onkeypress="return onSearchKeyEnter(event, this, '_self', 'search', 0);"/>
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
