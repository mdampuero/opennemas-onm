{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script language="Javascript" type="text/javascript">
// FIXME: fix toolbar
submitForm = function() {
    document.getElementById('formulario').submit();
};
</script>
{/block}

{block name="content" append}
<div style="width:70%;margin:0 auto;">
    <form action="widget.php" method="post" name="formulario" id="formulario" style="width:70%;margin:0 auto;">
        <input type="hidden" id="id" name="id" value="{$widget->id}" />
        <input type="hidden" id="action" name="action" value="save" />
        <div id="menu-acciones-admin">
            <div style="float: left; margin-left: 10px; margin-top: 10px;"><h2>{if $smarty.request.action eq "new"}{t}Creating new widget{/t}{else}{t 1=$widget->title}Editing widget «%1»{/t}{/if}</h2></div>
            <ul>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:submitForm();" value="Guardar" title="{t}Save{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}save.gif" title="{t}Save{/t}" alt="{t}Save{/t}" /><br />
                        {t}Save{/t}
                    </a>
                </li>
                <li>
                    <a href="?action=list" class="admin_add" value="{t}Cancel{/t}" title="{t}Cancel{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="{t}Cancel{/t}" alt="{t}Cancel{/t}" /><br />
                        {t}Cancel{/t}
                    </a>
                </li>
            </ul>
        </div>

        <div id="warnings-validation"></div>

        <table class="adminheading">
            <tbody>
                <tr>
                    <th>{t}Widget data{/t}</th>
                </tr>
            </tbody>
        </table>
        <table border="0" cellpadding="0" cellspacing="0" class="adminlist fuente_cuerpo">
        <tbody>
        <tr>
            <td valign="top" align="right" style="padding:4px;" width="150px">
                <label for="title">{t}Widget name{/t}:</label>
            </td>
            <td>
                <input type="text" id="title" value="{$widget->title}" name="title" title="Nombre del widget" class="required" size="30" maxlength="60" />
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="available">{t}Published{/t}:</label>
            </td>
            <td>
                <select name="available" id="available">
                    <option value="1" {if $widget->available == 1}selected="selected"{/if}>Si</option>
                    <option value="0" {if $widget->available == 0}selected="selected"{/if}>No</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="renderlet">{t}Widget type{/t}:</label>
            </td>
            <td>
                <select name="renderlet" id="renderlet">
                    <option value="html" {if $widget->renderlet == 'html'}selected="selected"{/if}>HTML</option>
                    <option value="php" {if $widget->renderlet == 'php'}selected="selected"{/if}>PHP</option>
                    <option value="smarty" {if $widget->renderlet == 'smarty'}selected="selected"{/if}>Smarty</option>
                </select>
            </td>
        </tr>
        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="metadata">{t}Keywords{/t}:</label>
            </td>
            <td>
                <input type="text" name="metadata" id="metadata" value="{$widget->metadata}" />
            </td>
        </tr>

        <tr>
            <td valign="top" align="right" style="padding:4px;">
                <label for="description">{t}Description{/t}:</label>
            </td>
            <td>
                <textarea name="description" id="description" cols="80" rows="5">{$widget->description}</textarea>
            </td>
        </tr>

        <tr class="widget-content">
            <td valign="top" align="right" style="padding:4px;">
                <label>{t}Content{/t}:</label>
            </td>
            <td>
                <textarea cols="80" rows="20" name="content">{$widget->content}</textarea>
                <br/><br/>
            </td>
        </tr>

        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="center">
                    <br/><br/>
                </td>
            </tr>
        </tfoot>
        </table>
    </form>
</div>
{/block}
