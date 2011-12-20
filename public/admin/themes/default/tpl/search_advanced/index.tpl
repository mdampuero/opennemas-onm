{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Advanced search{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

        <table class="adminheading">
            <tr>
                <th nowrap>{t}Advanced Search{/t}</th>
            </tr>
        </table>
        <table class="adminform">

            <tr>
                <td colspan="2" style="padding:20px; text-align:center" nowrap="nowrap" colspan='3'>
                    <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
                            class="required" size="80%" onkeypress="return onSearchKeyEnter(event, this, '_self', 'search', 0);"/> &nbsp;
                    <button type="submit" class="onm-button green" onclick="return enviar(this, '_self', 'search', 0);">{t}Search{/t}</button>
                </td>
            </tr>
            <tr>
                <td style="padding:20px; padding-top:0; vertical-align:middle; text-align:center" nowrap="nowrap">
                    {foreach name=contentTypes item=type from=$arrayTypes}
                        {if $type[0] == 1 || $type[0] == 4}
                            <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" valign="center" checked="true"/>{$type[2]|mb_capitalize}
                        {else}
                            <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" valign="center"/>{$type[2]|mb_capitalize}
                        {/if}
                    {/foreach}
                </td>
            </tr>
        </table>
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </form>

</div>
{/block}
