{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
jQuery(document).ready(function($){
    var newsletterType = $('#newsletter_subscriptionType');
    var reCaptcha = '{$missing_recaptcha}';

    //If selected manage newsletter by e-mail, show e-mail address field
    newsletterType.on('change', function() {
        var divExternal= $('.external-config');
        var divInternal = $('.internal-config');
        if ($(this).val() == 'submit') {
            divExternal.css('display', 'table-row');
            divInternal.css('display', 'none');
        } else {
            divExternal.css('display', 'none');
            divInternal.css('display', 'table-row');
        }
    });



    //If newsletter is activated and recaptcha is missing don't send form
    $('#formulario').on('submit', function(){
        if (reCaptcha) {
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
    <div class="top-action-bar clearfix">
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
                    <label for="sender" class="control-label">{t}Email from{/t}</label>
                    <div class="controls">
                        <input type="text" id="sender" name="newsletter_maillist[sender]" value="{$configs['newsletter_maillist']['sender']|default:""}" class="input-xlarge"/>
                    <div class="help-block">{t escape=off}Email sender{/t} (From)</div>
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
            </fieldset>
            <hr>

            <fieldset>
                <div class="control-group">
                    <label for="newsletter_subscriptionType" class="control-label">{t}Newsletter type{/t}</label>
                    <div class="controls">
                        <select name="newsletter_subscriptionType" id="newsletter_subscriptionType">
                            <option value="submit" {if $configs['newsletter_subscriptionType'] eq 'submit'} selected {/if}>{t}External Send{/t}</option>
                            <option value="create_subscriptor" {if $configs['newsletter_subscriptionType'] eq 'create_subscriptor'} selected {/if}>{t}Internal Send{/t}</option>
                        </select>
                        <div class="help-block">
                            {t escape=off}You can choose if receive
                            new subscriptions with a checking email or using the
                            <a href="{url name=admin_newsletter_subscriptions}" target="_blank">
                                table of subscribers from the application</a>.{/t}
                        </div>
                    </div>
                </div>

                <div class="external-config" {if $configs['newsletter_subscriptionType'] neq 'submit'}style="display:none"{/if}>

                    <div class="control-group">
                        <label for="email" class="control-label">{t}Mailing list address{/t}</label>
                        <div class="controls">
                            <input type="email" name="newsletter_maillist[email]" value="{$configs['newsletter_maillist']['email']|default:""}" id="email" class="input-xlarge" />
                            <div class="help-block">{t}If you have a mailing list service to deliver newsletters add the address here{/t}</div>
                        </div>
                    </div>

                    <div class="control-group" >
                        <label for="subscription" class="control-label">{t}Mail address to receive new subscriptions{/t}</label>
                        <div class="controls">
                            <input type="text" id="subscription" name="newsletter_maillist[subscription]" value="{$configs['newsletter_maillist']['subscription']|default:""}" class="input-xlarge" />
                        </div>
                    </div>
                </div>
                <div class="internal-config"  {if $configs['newsletter_subscriptionType'] neq 'create_subscriptor'}style="display:none"{/if}>
                    <div class="control-group">
                         <div class="controls">  </div>
                    </div>
                </div>
            </fieldset>
        </div>

   </div>
</form>
{/block}
