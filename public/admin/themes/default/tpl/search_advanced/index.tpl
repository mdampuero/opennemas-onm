{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

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
        <table class="adminlist" style="width:100%" >

            <tr>
                <td colspan="2" style="padding:20px;" nowrap="nowrap" colspan='3'>
                    <label for="title" >{t}Search in the information catalog:{/t}</label><br/><br/>
                    <input type="text" id="stringSearch" name="stringSearch" title="stringSearch"
                            class="required" size="80%" onkeypress="return onSearchKeyEnter(event, this, '_self', 'search', 0);"/>
                </td>
            </tr>
            <tr>
                <td style="padding:20px; padding-top:0; vertical-align:middle" nowrap="nowrap">
                    {foreach name=contentTypes item=type from=$arrayTypes}
                    {*NO interviu(10), NO albums(7), NO video(9), NO encuesta(11), NO kiosko(14), NO eventos(5)*}
                        {if $type[0] != 5 && $type[0] != 10 && $type[0] != 11 && $type[0] != 14 && $type[0] != 7 && $type[0] != 9}
                            {if $type[0] == 1 || $type[0] == 4}
                                <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" valign="center" checked="true"/>{$type[2]|mb_capitalize}
                            {else}
                                <input class="{$type[0]}" name="{$type[1]}" id="{$type[1]}" type="checkbox" valign="center"/>{$type[2]|mb_capitalize}
                            {/if}
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
