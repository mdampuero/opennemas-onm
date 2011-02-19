{extends file="base/admin.tpl"}

{block name="content"}
<div style="width:70%; margin: 0 auto;">

    <div id="menu-acciones-admin">
        <div style="float: left; margin-left: 10px; margin-top: 10px;">
            <h2>{t}File Manager :: Upload{/t}</h2>
        </div>
        <ul>
            <li>
                <a href="{$_SERVER['PHP_SELF']}?action=list" class="admin_add" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" ><br />{t}Cancel{/t}
                </a>
            </li>
        </ul>
    </div>

    <form action="{$_SERVER['PHP_SELF']}" method="POST" enctype="multipart/form-data" style="margin-top:10px !important;">

        {if $message neq ""}
            <div class="error">
                {$message}
            </div>
        {/if}

        <table class="adminheading">
            <tr>
                <th>&nbsp;</th>
            </tr>
        </table>
        <table class="adminlist">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td >
                        <h3>{t}Add a title and attach a file from the form below.{/t}</h3>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}Title{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input type="text" id="title" name="title" title="Título" autocomplete="off" value="" class="required" size="50" />

                        <input type="hidden" id="category" name="category" value="{$smarty.request.category}" />
                        <input type="hidden" id="related"  name="related"  value="{$smarty.request.related}" />
                        <input type="hidden" id="desde"    name="desde"    value="{$smarty.request.desde}" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}File:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input id="path" name="path" type="file" autocomplete="off"/>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td style="padding-bottom:20px">
                        <input id="op" name="op" type="submit" value="{t}Upload{/t}" />
                    </td>
                </tr>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan=2>&nbsp;</td>
                </tr>
            </tfoot>
        </table>
        <input type="hidden" id="action" name="action" value="upload" />
        <input type="hidden" id="category" name="category" value="{$category}" />
    </form>

</div>
{/block}
