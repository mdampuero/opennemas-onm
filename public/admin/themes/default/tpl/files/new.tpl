{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Images manager :: General statistics{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$_SERVER['PHP_SELF']}?action=list" class="admin_add" value="Cancelar" title="Cancelar">
                    <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
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

                    </td>
                </tr>

            </tbody>
        </table>
        <div class="action-bar clearfix">
            <div class="right">
                <button type="submit" class="onm-button red">{t}Upload{/t}</button>
            </div>
        </div>
        <input type="hidden" id="action" name="action" value="upload" />
        <input type="hidden" id="category" name="category" value="{$category}" />
    </form>

</div>
{/block}
