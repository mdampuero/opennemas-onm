{extends file="base/admin.tpl"}

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
            $('#warnings-validation').replaceWith('<div class="alert alert-error">{t escape=off}Before using newsletter you have to fill the <a href="{url name=admin_system_settings}#external"  target="_blank">reCaptcha keys on system settings</a>{/t}</div>');
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
                $('#warnings-validation').replaceWith('<div class="alert alert-error">{t escape=off}Before using newsletter you have to fill the <a href="{url name=admin_system_settings}#external"  target="_blank">reCaptcha keys on system settings</a>{/t}</div>');

            }
            return false;
        }
    });
});
</script>
{/block}

{block name="content"}
<form action="{url name=admin_newsletter_config}" method="POST" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Newsletters{/t} :: {t}Configuration{/t}</h2></div>
            <ul class="old-button">
                <li>
                    <button type="submit">
                        <img src="{$params.IMAGE_DIR}save.png" alt="{t}Save{/t}"><br>{t}Save{/t}
                    </button>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_newsletters}" class="admin_add" title="{t}Go back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" title="{t}Sync list  with server{/t}" alt="{t}Go back to list{/t}" ><br />{t}Go back to list{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}
        <div id="warnings-validation"></div>


        <div class="form-horizontal panel">
            <fieldset>
            <div class="control-group">
                <label for="name" class="control-label">{t}Newsletter subject{/t}</label>
                <div class="controls">
                    <input type="text" id="name" name="newsletter_maillist[name]" value="{$configs['newsletter_maillist']['name']|default:""}" class="input-xlarge"/>
                    <div class="help-block">{t}The subject of the emails in this bulletin{/t}</div>
                </div>
             </div>

            <div class="control-group">
                <label for="email" class="control-label">{t}Mailing list address{/t}</label>
                <div class="controls">
                    <input type="email" name="newsletter_maillist[email]" value="{$configs['newsletter_maillist']['email']|default:""}" id="email" class="input-xlarge" />
                    <div class="help-block">{t}If you have a mailing list service to deliver newsletters add the address here{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="newsletter_maillist_link" class="control-label">{t}Newsletter links points to{/t}</label>
                <div class="controls">
                    <select name="newsletter_maillist[link]" id="newsletter_maillist_link">
                        <option value="inner" {if $configs['newsletter_maillist']['link'] eq 'inner'} selected {/if}>{t}Point to inner{/t}</option>
                        <option value="front" {if $configs['newsletter_maillist']['link'] eq 'front'} selected {/if}>{t}Point to frontpage{/t}</option>
                    </select>
                    <div class="help-block">{t}You can choose if you prefer that the links of the contents of the bulletin point within the content or contents on the frontpage{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="newsletter_subscriptionType" class="control-label">{t}Newsletter subscription type{/t}</label>
                <div class="controls">
                    <select name="newsletter_subscriptionType" id="newsletter_subscriptionType">
                        <option value="submit" {if $configs['newsletter_subscriptionType'] eq 'submit'} selected {/if}>{t}Manage newsletter by e-mail{/t}</option>
                        <option value="create_subscriptor" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'} selected {/if}>{t}Manage newsletter by subscriptors table{/t}</option>
                    </select>
                    <div class="help-block">
                        {t escape=off}You can choose to receive new subscriptions with a checking email or using the <a href="{url name=admin_newsletter_subscriptions}" target="_blank">table of subscribers of the application</a>.
                        </br>If you choose email subscription, you must enter the address on a field that will appear{/t}
                    </div>
                </div>
            </div>

            <div class="control-group" id="sub-mail" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'}style="display:none"{/if}>
                <label for="subscription" class="control-label">{t}Mail address to receive new subscriptions{/t}</label>
                <div class="controls">
                    <input type="text" id="subscription" name="newsletter_maillist[subscription]" value="{$configs['newsletter_maillist']['subscription']|default:""}" class="input-xlarge" />
                </div>
            </div>

            <div class="control-group">
                <label for="sender" class="control-label">{t}Mail sender{/t}</label>
                <div class="controls">
                    <input type="text" id="sender" name="newsletter_maillist[sender]" value="{$configs['newsletter_maillist']['sender']|default:""}" class="input-xlarge"/>
                <div class="help-block">{t escape=off}Verify that the domain has enabled <a href="http://en.wikipedia.org/wiki/Sender_Policy_Framework" target="_blank">SPF</a> settings for sending{/t}</div>
                </div>
            </div>

            <div class="control-group">
                <label for="sender" class="control-label">{t}Enable frontpage subscription{/t}</label>
                <div class="controls">
                    <select name="newsletter_enable" id="newsletter_enable">
                        <option value="yes" {if $configs['newsletter_enable'] eq 'yes'} selected {/if}>{t}Enabled{/t}</option>
                        <option value="no" {if $configs['newsletter_enable'] eq 'no' || is_null($configs['newsletter_enable'])} selected {/if}>{t}Disabled{/t}</option>
                    </select>
                    <div class="help-block">{t}If enabled, a link to newsletter subscription will appear at home frontpage header{/t}</div>
                </div>
            </div>
            </fieldset>
        </div>

   </div>
</form>
{/block}
