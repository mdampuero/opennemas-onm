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
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Ads :: Configuration{/t}</h2></div>
            <ul class="old-button">
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

        <div id="{$category}">

            <table class="adminform" border="0">
                <tr>
                    <td>
                        <div class="form-wrapper">
                            <div>
                                <label for="ads_settings_lifetime_cookie">{t}Cookie lifetime for intersticials (min):{/t}</label>
                                <input type="text" class="required" name="ads_settings_lifetime_cookie" id="ads_settings_lifetime_cookie" value="{$configs['ads_settings']['lifetime_cookie']|default:'300'}" />
                            </div>
                            <br />


                            <br />


                        </div>
                    </td>
                    <td> <br/>
                        <div class="onm-help-block">
								<div class="title"><h4>{t}Definition values{/t}</h4></div>
                                <div class="content">
                                    <ul>
                                        <li>{t}Use cookie lifetime to define how long it will take to re-display the interstitial in frontpage (in minutes){/t}</li>
                                    </ul>
                                </div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button red">
                </div><!-- / -->
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="config" />
    </div>
</form>

{/block}
