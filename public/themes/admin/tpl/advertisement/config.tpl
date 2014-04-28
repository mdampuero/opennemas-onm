{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:150px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:300px;
    }
    .form-wrapper {
        margin:10px auto;
        width:90%;
    }
    .help-block {
        max-width: 300px;
    }
    </style>
{/block}

{block name="content"}
<form action="{url name=admin_ads_config}" method="POST">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Ads :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}" ><br>{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_ads}" class="admin_add" value="{t}Go back to list{/t}" title="{t}Go back to list{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back to list{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="{$category}" class="form-horizontal panel">

            <div class="control-group">
                <label for="ads_settings_lifetime_cookie" class="control-label">{t}Cookie lifetime for intersticials{/t}</label>
                <div class="controls">
                    <input type="number" class="required" name="ads_settings_lifetime_cookie" id="ads_settings_lifetime_cookie" value="{$configs['ads_settings']['lifetime_cookie']|default:'300'}" />
                    <div class="help-inline">in minutes</div>
                    <div class="help-block">{t}This setting indicates how long will take to re-display the interstitial in frontpage.{/t}</div>
                </div>
            </div>
            <div class="control-group">
                <label for="ads_settings_no_generics" class="control-label">{t}Allow generic advertisement{/t}</label>
                <div class="controls">
                    <select name="ads_settings_no_generics" id="ads_settings_no_generics">
                        <option value="0">{t}Yes{/t}</option>
                        <option value="1" {if $configs['ads_settings']['no_generics'] eq "1"} selected {/if}>{t}No{/t}</option>
                    </select>
                    <div class="help-block">{t}This settings allow printing home ads when ads in category are empty.{/t}</div>
                </div>
            </div>

            <fieldset>
                <h3 class="settings-header">{t}OpenX/Revive Ad server integration{/t}</h3>
                <div class="control-group">
                    <label for="revive_ad_server_url" class="control-label">{t}Ad server base url{/t}</label>
                    <div class="controls">
                        <input type="text" name="revive_ad_server_url" value="{$configs['revive_ad_server']['url']}">
                        <div class="help-block">{t}The ad server URL (i.e. http://ad.serverexample.net/).{/t}</div>
                    </div>
                </div>
            </fieldset>
        </div>
        <input type="hidden" id="action" name="action" value="config" />
    </div>
</form>

{/block}
