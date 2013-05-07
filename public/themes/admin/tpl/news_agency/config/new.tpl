{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('.check-pass').on('click', function(){
        var passInput = $('#password');
        if ($(this).is(':checked')) {
            passInput.prop('type','text');
        } else {
            passInput.prop('type','password');
        }
    });
});
</script>
{/block}

{block name="content"}
<form action="{if array_key_exists('id', $server)}{url name=admin_news_agency_server_update id=$server['id']}{else}{url name=admin_news_agency_server_create}{/if}"
    method="POST" class="form-horizontal">
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}News agency{/t} :: {if array_key_exists('id', $server)}{t}Update source{/t}{else}{t}Add source{/t}{/if}</h2></div>
        <ul class="old-button">
            <li>
                <button action="submit">
                    <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save and exit{/t}"><br />{t}Save{/t}
                </button>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_news_agency_servers}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="panel">
        <div class="control-group">
            <label for="name" class="control-label">{t}Source name{/t}</label>
            <div class="controls">
                <input type="text" id="server" name="name" value="{$server['name']}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="activated" class="control-label">{t}Activated{/t}</label>
            <div class="controls">
                <input name="activated" type="checkbox" {if $server['activated'] != 0}checked{/if} value='1' >
            </div>
        </div>

        <div class="control-group">
            <label for="url" class="control-label">{t}Url{/t}</label>
            <div class="controls">
                <input type="text" id="server" name="url" value="{$server['url']}" class="input-xxlarge" required="required"/>
                <div class="help-block">{t}The server url for this source. Example: ftp://server.com/path{/t}</div>
            </div>
        </div>

        <div class="control-group">
            <label for="username" class="control-label">{t}Username{/t}</label>
            <div class="controls">
                <input type="text" id="username" name="username" value="{$server['username']}" class="input-xlarge" required="required"/>
            </div>
        </div>

        <div class="control-group">
            <label for="password" class="control-label">{t}Password{/t}</label>
            <div class="controls">
                <input type="password" id="password" name="password" value="{$server['password']}" class="input-xlarge" required="required"/>
                <input type="checkbox" class="check-pass" value="">&nbsp;{t}Show password{/t}
            </div>
        </div>

        <div class="control-group">
            <label for="agency_string" class="control-label">{t}Agency{/t}</label>
            <div class="controls">
                <input type="text" id="agency_string" name="agency_string" value="{$server['agency_string']}" class="input-xlarge" required="required"/>
                <div class="help-block">{t}When importing elements this will be the signature{/t}</div>
            </div>
        </div>

        <div class="control-group">
            <label for="sync_from" class="control-label">{t}Sync elements newer than{/t}</label>
            <div class="controls">
                <select name="sync_from" required="required">
                    {html_options options=$sync_from selected={$server['sync_from']}}
                </select>
                <div class="help-block">{t escape=off}Set this to you preferences to fetch elements since a fixed date.<br>Less time means faster synchronizations.{/t}</div>
            </div>
        </div>
    </div>
</div>
</form>
{/block}
