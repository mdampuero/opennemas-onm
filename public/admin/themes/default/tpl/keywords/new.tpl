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
        <form class="form-horizontal span8">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="pclave">{t}Keyword name{/t}</label>
                    <div class="controls">
                        <input type="text" id="pclave" name="pclave" title="Palabra clave" value="{$pclave->pclave|default:""}"
                               class="required input-xlarge" size="30" maxlength="60" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="tipo">{t}Type{/t}</label>
                    <div class="controls">
                        <select name="tipo" id="tipo">
                            {html_options options=$tipos selected=$pclave->tipo|default:""}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="value">{t}Value{/t}</label>
                    <div class="controls">
                       <input type="text" id="value" name="value" title="Valor" value="{$pclave->value|default:""}"
                               class="required input-xlarge"/>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">{t}Save{/t}</button>
                </div>
            </fieldset>
        </form>
    </div>

    <input type="hidden" id="action" name="action" value="save" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>

</div>
{/block}
