{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    var searching = false;

    new Form.Element.Observer(
        'pclave',
        0.4,
        function() {
            var valor = $('pclave').value;
            if((valor.length >= 4) && (!searching)) {
                searching = true;
                new Ajax.Updater('similarKeywords', '?action=search&q='+valor+'&id='+$('id').value, {
                    onSuccess: function() {
                        searching = false;
                    }
                });
            }
        }
    );
</script>
{/block}

{block name="content"}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">


    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Keyword Manager{/t} :: {t}Editing keyword information{/t}</h2></div>
            <ul class="old-button">
               <li>
                    <a href="?action=list" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <th>{t}Keyword information{/t}</th>
            </tr>
        </table>
		<table class="adminform">
            <tbody>
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="40%">
                    <label for="name">Palabra clave:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="60%">
                    <input type="text" id="pclave" name="pclave" title="Palabra clave" value="{$pclave->pclave|default:""}"
                           class="required" size="30" maxlength="60" />
                </td>
            </tr>
            <tr>
                <th valign="top" align="right" style="padding:4px;" width="40%">
                    <label for="tipo">Tipo:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="60%">
                    <select name="tipo" id="tipo">
                        {html_options options=$tipos selected=$pclave->tipo|default:""}
                    </select>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right" style="padding:4px;" width="40%">
                    <label for="value">Valor:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" width="60%">
                    <input type="text" id="value" name="value" title="Valor" value="{$pclave->value|default:""}"
                           size="50" maxlength="240" />
                </td>
            </tr>

            <tr>
                <td valign="top" align="right" style="padding:4px;">
                    <label>Existing similar elements yet: </label>
                </td>
                <td valign="top" style="padding:4px;">
                    <div id="similarKeywords" style="border: 1px solid #CCC; padding: 10px; width: 400px; background-color: #EEE;"></div>
                </td>
            </tr>

            </tbody>
		</table>
        <div class="action-bar clearfix">
            <div class="right">
                <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
            </div>
        </div>
    </div>

    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>

</div>
{/block}
