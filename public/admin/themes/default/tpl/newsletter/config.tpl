{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    label {
        width:200px;
        padding-left:10px;
        display:inline-block;
    }
    input[type="text"],
    input[type="password"] {
        width:400px;
    }
    th {
        vertical-align: top;
        text-align: left;
        padding: 10px;
        width: 200px;
        font-size: 13px;
    }
    .form-wrapper {
        margin:10px auto;
        width:70%;
    }
    </style>
{/block}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($){
    var newsletterStatus = $('#newsletter_enable');
    var newsletterType = $('#newsletter_subscriptionType');
    var reCaptcha = '{$missing_recaptcha}';

    //If selected manage newsletter by e-mail, show e-mail address field
    newsletterType.on('change', function() {
        var divMail = $('#sub-mail');

        if ($(this).val() == 'submit') {
            divMail.css('display', 'table-row');
        } else {
            divMail.css('display', 'none');
        }
    });

    //If newsletter is changed to activated and recaptcha is missing show warning
    newsletterStatus.on('change', function() {
        if ($(this).val() == 'yes' && reCaptcha) {
            $('#warnings-validation').replaceWith('<div class="notice">{t escape=off}Before using newsletter you have to fill the <a href="/admin/controllers/system_settings/system_settings.php#external"  target="_blank">reCaptcha keys on system settings</a>{/t}</div>');
        } else {
            if ($('div.notice')) {
                $('div.notice').replaceWith('<div id="warnings-validation"></div>');
            }
        }
    });

    //If newsletter is activated and recaptcha is missing don't send form
    $('#formulario').on('submit', function(){
        if (newsletterStatus.val() == 'yes' && reCaptcha) {
            if ($('#warnings-validation')) {
                $('#warnings-validation').replaceWith('<div class="notice">{t escape=off}Before using newsletter you have to fill the <a href="/admin/controllers/system_settings/system_settings.php#external"  target="_blank">reCaptcha keys on system settings</a>{/t}</div>');

            }
            return false;
        }
    });
});
</script>
{/block}

{block name="content"}
<form action="{$smarty.server.PHP_SELF}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletter :: Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="{$smarty.server.PHP_SELF}" class="admin_add" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}
        <div id="warnings-validation"></div>

        <div id="cat-{$category}">

            <table class="adminheading">
                 <tr>
                     <th>{t}Information about newsletter receipt{/t}</th>
                 </tr>
            </table>

            <table class="adminform">
                <tr>
                    <th scope="row">
                        <label>{t}Newsletter subject{/t}:</label>
                    </th>
                    <td>
                        <input type="text" name="newsletter_maillist[name]" value="{$configs['newsletter_maillist']['name']|default:""}" />
                    </td>
                    <td rowspan="5">
                        <div class="help-block margin-left-1">
                            <div class="title"><h4>{t}Basic parameters{/t}</h4></div>
                            <div class="content">
                                <dl>
                                    <dt><strong>{t}Newsletter subject{/t}</strong></dt>
                                    <dd>{t}The subject of the emails in this bulletin{/t}</dd>
                                    <dt><strong>{t}Mailing list address{/t}</strong></dt>
                                    <dd>{t}If you have a mailing list service to deliver newsletters add the address here{/t}</dd>
                                    <dt><strong>{t}Newsletter links points to{/t}</strong></dt>
                                    <dd>{t}You can choose if you prefer that the links of the contents of the bulletin point within the content or contents on the frontpage{/t}</dd>
                                    <dt><strong>{t}Newsletter subscription type{/t}</strong></dt>
                                    <dd>{t escape=off}You can choose to receive new subscriptions with a checking email or using the <a href="/admin/controllers/newsletter/subscriptors.php?action=list" target="_blank">table of subscribers of the application</a>.
                                    </br>If you choose email subscription, you must enter the address on a field that will appear{/t}</dd>
                                    <dt><strong>{t}Enable frontpage subscription{/t}</strong></dt>
                                    <dd>{t}If enabled, a link to newsletter subscription will appear at home frontpage header{/t}</dd>
                                </dl>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>{t}Mailing list address{/t}:</label>
                    </th>
                    <td colspan="2">
                        <input type="text" name="newsletter_maillist[email]" value="{$configs['newsletter_maillist']['email']|default:""}" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>{t}Newsletter links points to{/t}:</label>
                    </th>
                    <td colspan="2">
                        <select name="newsletter_maillist[link]" id="newsletter_maillist[link]">
                            <option value="inner" {if $configs['newsletter_maillist']['link'] eq 'inner'} selected {/if}>{t}Point to inner{/t}</option>
                            <option value="front" {if $configs['newsletter_maillist']['link'] eq 'front'} selected {/if}>{t}Point to frontpage{/t}</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>{t}Newsletter subscription type{/t}:</label>
                    </th>
                    <td colspan="2">
                        <select name="newsletter_subscriptionType" id="newsletter_subscriptionType">
                            <option value="submit" {if $configs['newsletter_subscriptionType'] eq 'submit'} selected {/if}>{t}Manage newsletter by e-mail{/t}</option>
                            <option value="create_subscriptor" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'} selected {/if}>{t}Manage newsletter by subscriptors table{/t}</option>
                        </select>
                    </td>
                </tr>
                <tr id="sub-mail" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'}style="display:none"{/if}>
                    <div >
                        <th scope="row">
                            <label>{t}Mail address to receive new subscriptions{/t}:</label>
                        </th>
                        <td colspan="2">
                            <input type="text" name="newsletter_maillist[subscription]" value="{$configs['newsletter_maillist']['subscription']|default:""}" />
                        </td>
                    </div>
                </tr>
                <tr>
                    <th scope="row">
                        <label>{t}Enable frontpage subscription{/t}:</label>
                    </th>
                    <td colspan="2">
                        <select name="newsletter_enable" id="newsletter_enable">
                            <option value="yes" {if $configs['newsletter_enable'] eq 'yes'} selected {/if}>{t}Enabled{/t}</option>
                            <option value="no" {if $configs['newsletter_enable'] eq 'no' || is_null($configs['newsletter_enable'])} selected {/if}>{t}Disabled{/t}</option>
                        </select>
                    </td>
                </tr>
            </table>
            <div class="action-bar clearfix">
                <div class="right">
                    <input type="submit" name="submit" value="{t}Save{/t}"  class="onm-button green">
                </div>
            </div>
        </div>

        <input type="hidden" id="action" name="action" value="save_config" />
   </div>
</form>
{/block}
