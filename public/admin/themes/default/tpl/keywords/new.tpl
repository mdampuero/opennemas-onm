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
<form id="formulario" name="formulario" action="{if $keyword->id}{url name=admin_keyword_update id=$keyword->id}{else}{url name=admin_keyword_create}{/if}" method="POST">

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Keyword Manager{/t} :: {t}Editing keyword information{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button action="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and continue{/t}"><br />{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_keywords}" class="admin_add" value="{t}Go back{/t}" title="{t}Go back{/t}">
                        <img src="{$params.IMAGE_DIR}previous.png"><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="form-horizontal panel">
            <fieldset>
                <div class="control-group">
                    <label class="control-label" for="pclave">{t}Name{/t}</label>
                    <div class="controls">
                        <input type="text" id="pclave" name="pclave" value="{$keyword->pclave|default:""}"
                               class="required input-xlarge" size="30" maxlength="60" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="tipo">{t}Type{/t}</label>
                    <div class="controls">
                        <select name="tipo" id="tipo">
                            {html_options options=$tipos selected=$keyword->tipo|default:""}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="value">{t}Value{/t}</label>
                    <div class="controls">
                       <input type="text" id="value" name="value" value="{$keyword->value|default:""}" class="required input-xlarge"/>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
{/block}
