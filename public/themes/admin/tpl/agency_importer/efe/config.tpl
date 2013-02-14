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
<form action="{url name=admin_importer_efe_config}" method="POST" id="formulario">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE articles{/t} :: {t}Configuration{/t}</h2></div>
        <ul class="old-button">
            <li>
                <button action="submit">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_importer_efe}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="form-horizontal panel">
        <div class="control-group">
            <label for="server" class="control-label">{t}Server{/t}</label>
            <div class="controls">
                <input type="text" id="server" name="server" value="{$server}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="username" class="control-label">{t}Username{/t}</label>
            <div class="controls">
                <input type="text" id="username" name="username" value="{$username}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="password" class="control-label">{t}Password{/t}</label>
            <div class="controls">
                <input type="text" id="password" name="password" value="{$password}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="agency_string" class="control-label">{t}Agency{/t}</label>
            <div class="controls">
                <input type="text" id="agency_string" name="agency_string" value="{$agency_string}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="sync_from" class="control-label">{t}Sync elements newer than{/t}</label>
            <div class="controls">
                <select name="sync_from" required="required">
                    {html_options options=$sync_from selected=$sync_from_setting|default:""}
                </select>
            </div>
        </div>
    </div>
</div>
</form>
{/block}
