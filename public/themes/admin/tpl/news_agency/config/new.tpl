{extends file="base/admin.tpl"}

{block name="header-css" append}
    {css_tag href="jquery.simplecolorpicker.css" basepath="js/jquery/jquery_simplecolorpicker/"}
{/block}

{block name="footer-js" append}
    {script_tag src="/jquery/jquery_simplecolorpicker/jquery.simplecolorpicker.js"}
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#formulario').onmValidate({
        'lang' : '{$smarty.const.CURRENT_LANGUAGE|default:"en"}'
    });

    $('.check-pass').on('click', function(e, ui){
        e.preventDefault();
        var passInput = $('#password');
        var btn = $(this);
        if (passInput.attr('type') == 'password') {
            passInput.prop('type','text');
            btn.html('{t}Hide password{/t}');
        } else {
            passInput.prop('type','password');
            btn.html('{t}Show password{/t}');
        }
    });

    $('select[name="colorpicker"]').simplecolorpicker(
        'selectColor', $('#color').val()
    ).on('change', function() {
        $('#color').val($('select[name="colorpicker"]').val());
    });

});
</script>
{/block}

{block name="content"}
<form action="{if array_key_exists('id', $server)}{url name=admin_news_agency_server_update id=$server['id']}{else}{url name=admin_news_agency_server_create}{/if}"
    method="POST" class="form-horizontal" autocomplete="off">
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
                <button class="check-pass">{t}Show password{/t}</button>
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
            <label for="author" class="control-label">{t}Import authors{/t}</label>
            <div class="controls">
                <input name="author" type="checkbox" {if $server['author'] != 0}checked{/if} value='1' >
                <div class="help-block">{t}Activate this if you want to import the author of the elements if available{/t}</div>
            </div>
        </div>

        <div class="control-group">
            <label for="color" class="control-label">{t}Color{/t}</label>
            <div class="controls">
                <input type="hidden" id="color" name="color" value="{$server['color']|default:'#424E51'}" class="input-xlarge"/>
                <select name="colorpicker">
                    <option value="#424E51">{t}Dark Gray{/t}</option>
                    <option value="#000000">{t}Black{/t}</option>
                    <option value="#980101">{t}Bold red{/t}</option>
                    <option value="#7bd148">{t}Green{/t}</option>
                    <option value="#0000FF">{t}Blue{/t}</option>
                    <option value="#46d6db">{t}Turquoise{/t}</option>
                    <option value="#7ae7bf">{t}Light green{/t}</option>
                    <option value="#51b749">{t}Bold green{/t}</option>
                    <option value="#fbd75b">{t}Yellow{/t}</option>
                    <option value="#FF8C00">{t}Orange{/t}</option>
                    <option value="#dc2127">{t}Red{/t}</option>
                    <option value="#dbadff">{t}Purple{/t}</option>
                </select>
                <div class="help-block">{t}Color to distinguish between other agencies{/t}</div>
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
