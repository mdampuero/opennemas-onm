{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: General statistics{/t}</h2></div>
        <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=list" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <form action="{$smarty.server.PHP_SELF}" method="POST" enctype="multipart/form-data" style="margin-top:10px !important;">

        {if isset($message) && $message neq ""}
            <div class="error">
                {$message}
            </div>
        {/if}

        <table class="adminheading">
            <tr>
                <th>&nbsp;</th>
            </tr>
        </table>
        <table class="adminform">
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
                        <input type="text" id="title" name="title" title="Título" autocomplete="off"
                               onBlur="javascript:get_metadata(this.value);" value="" class="required" size="50" />

                        <input type="hidden" id="category" name="category" value="{$smarty.request.category|default:""}}" />
                        <input type="hidden" id="related"  name="related"  value="{$smarty.request.related|default:""}}" />
                        <input type="hidden" id="desde"    name="desde"    value="{$smarty.request.desde|default:""}}" />
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right" style="padding:4px;" width="30%">
                        <label for="title">{t}Keywords:{/t}</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" width="70%">
                        <input id="metadata" name="metadata" type="text" class="required" size="50"/>
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
