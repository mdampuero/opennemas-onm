{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="POST" name="newsletterForm" id="pick-recipients-form">
<div class="page-navbar actions-navbar">
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <ul class="nav quick-section">
                <li class="quicklinks">
                    <h4>
                        <i class="fa fa-home fa-lg"></i>
                        {t}Newsletters{/t}
                    </h4>
                </li>
                <li class="quicklinks hidden-xs"><span class="h-seperate"></span></li>
                <li class="quicklinks hidden-xs">
                  <h5>{t}Recipient selection{/t}</h5>
                </li>
            </ul>
            <div class="all-actions pull-right">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <a href="{url name=admin_newsletters}" class="btn btn-link" title="{t}Go back to list{/t}">
                            <span class="fa fa-reply"></span>
                        </a>
                    </li>
                    <li class="quicklinks"><span class="h-seperate"></span></li>
                    <li class="quicklinks btn-group">
                        <a class="btn btn-primary" href="{url name=admin_newsletter_preview id=$id}" class="admin_add" title="{t}Previous{/t}" id="prev-button">
                            <span class="fa fa-chevron-left"></span>
                            <span class="hidden-xs">{t}Previous{/t}</span>
                        </a>
                        <a class="btn btn-info confirm-send-button" data-controls-modal="modal-confirm-send" href="#" title="{t}Next{/t}" id="next-button">
                            <span class="fa fa-envelope"></span>
                            <span class="hidden-xs">{t}Send{/t}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="content newsletter-manager">

    {render_messages}

    <div class="grid simple">
        <div class="grid-title">
            {t}Please select your desired persons to sent the newsletter to.{/t}
        </div>
        <div class="grid-body">
            <div class="col-md-6">
                <div id="accounts-provider" class="tabs">
                    <ul>
                        {if $subscriptionType eq 'submit'}
                        <li><a href="#maillist-account">{t}MailList{/t}</a></li>
                        {elseif $subscriptionType eq 'create_subscriptor'}
                        <li><a href="#database-accounts">{t}Database accounts{/t}</a></li>
                        {/if}
                        <li><a href="#custom-accounts">{t}Custom{/t}</a></li>
                    </ul>
                    {if $subscriptionType eq 'submit'}
                    <div id="maillist-account">
                        <ul id="maillist-account-list">
                            {foreach name=d from=$mailList item=mail}
                            <li class="account"  data-email="{$mail->email}"  data-name="{$mail->name}">
                                {$mail->name}:{$mail->email}
                                <i class="icon icon-trash"></i>
                            </li>
                            {/foreach}
                        </ul>
                    </div>
                    {elseif $subscriptionType eq 'create_subscriptor'}
                    <div id="database-accounts">
                        <div class="btn-group pull-right">
                            <a id="button-check-all" href="#" class="btn">
                                <i class="icon-check"></i>{t}Select all{/t}
                            </a>
                            <a class="btn" id="add-selected" href="#">
                                <i class="icon-plus"></i>{t}Add selected{/t}
                            </a>
                        </div>
                        <ul id="database-accounts-list">
                            {foreach name=d from=$accounts item=account}
                                <li class="account"  data-email="{$account->email}" data-name="{$account->name}">
                                    <label>
                                        <input type="checkbox">
                                        {$account->name}:{$account->email}
                                        <i class="icon icon-trash"></i>
                                    </label>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {/if}
                    <div id="custom-accounts" class="form-vertical">
                        <div class="btn-group">
                            <a id="parse-and-add" href="#" class="btn">
                                <i class="icon-check"></i>{t}Parse list & add{/t}
                            </a>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <textarea id="othersMails" name="othersMails" placeholder="{t}Write a list of email address writing one per line (max 10).{/t}"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-1 center">
                <span class="fa fa-chevron-right fa-3x hidden-xs hidden-sm"  style="padding-top:100px"></span>
                <span class="fa fa-chevron-down fa-3x visible-xs visible-sm"  style="padding-top:100px"></span>
            </div>

            <div class="col-md-5">
                <p>{t}Receivers{/t}</p>
                <div id="recipients">
                    <ul id="items-recipients">
                        {if !empty($recipients)}
                        {foreach name=d from=$recipients item=recipient}
                        <li  data-email="{$recipient->email}" data-name="{$recipient->name}">
                            {$recipient->name}:{$recipient->email}
                        </li>
                        {/foreach}
                        {/if}
                    </ul>
                </div>
            </div>

            <input type="hidden" id="recipients_hidden" name="recipients" />

        </div>
    </div>

</div>
</form>
{/block}

{block name="footer-js"}
    {script_tag src="/onm/newsletter.js"}
    {include file="newsletter/modals/_confirm_send.tpl"}
{/block}
