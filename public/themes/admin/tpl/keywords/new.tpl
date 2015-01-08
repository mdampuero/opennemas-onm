{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });
});
</script>
{/block}

{block name="content"}
<form id="formulario" name="formulario" action="{if $keyword->id}{url name=admin_keyword_update id=$keyword->id}{else}{url name=admin_keyword_create}{/if}" method="POST">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Keywords{/t} :: {if isset($keyword->id)}{t}Editing keyword{/t}{else}{t}Creating keyword{/t}{/if}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
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
                               class="input-xlarge" size="30" maxlength="60" required="required"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="tipo">{t}Type{/t}</label>
                    <div class="controls">
                        <select name="tipo" id="tipo" required="required">
                            {html_options options=$tipos selected=$keyword->tipo|default:""}
                        </select>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="value">{t}Value{/t}</label>
                    <div class="controls">
                       <input type="text" id="value" name="value" value="{$keyword->value|default:""}" class="input-xlarge" required="required"/>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</form>
{/block}
