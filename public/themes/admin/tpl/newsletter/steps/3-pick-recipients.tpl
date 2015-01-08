{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .btn-group .btn {
        display:inline-block;
    }
    #accounts-provider > div,
    #items-recipients {
        height:500px;
        overflow-y: scroll;
    }


    #accounts-provider ul {
        margin-top:10px;
        display:block;
    }

    #othersMails {
        min-height:300px;
        margin-top:10px;
        width:96%;
    }

    #accounts-provider ul {
        margin:0px;
    }
    #accounts-provider ul,
    #items-recipients {
        list-style: none;
    }

    #accounts-provider ul li,
    #items-recipients li {
        padding:3px;
        border:1px solid #ccc;
        margin-bottom: 4px;
        position:relative;
    }

    #accounts-provider ul li .icon,
    #items-recipients li .icon {
        position:absolute;
        right:5px;
        top:3px;
        cursor: pointer;
    }

    #items-recipients li input[type=checkbox] { display:none;}

    #items-recipients {
        padding:10px;
        display:block;
        margin:0;
    }
    .placeholder-element {
        min-height:24px !important;
        background:#efefef !important;
        border:1px dashed Gray !important;
    }

    #database-accounts-list {
        margin:0px;
        margin-top:50px !important;
        min-height:300px;
    }
    #recipients {
        border:1px solid #ccc;
    }
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/onm/newsletter.js"}
{/block}

{block name="content"}

<form action="#" method="POST" name="newsletterForm" id="pick-recipients-form">
    <div class="page-navbar actions-navbar">
        <div class="navbar navbar-inverse">
            <div class="navbar-inner">
                <ul class="nav quick-section">
                    <li class="quicklinks">
                        <h4>
                            <i class="fa fa-home fa-lg"></i>
                            {t}Newsletters{/t} :: {t}Creating{/t} :: {t}Recipient selection{/t}
                        </h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="buttons-recipients" class="top-action-bar clearfix">
        <div class="wrapper-content">

            <ul class="old-button">

                <li>
                    <a class="confirm-send-button" data-controls-modal="modal-confirm-send" href="#" title="{t}Next{/t}" id="next-button">
                        <img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
                        {t}Send newsletter{/t}
                    </a>
                </li>
                 <li>
                    <a href="{url name=admin_newsletter_preview id=$id}" class="admin_add" title="{t}Previous{/t}" id="prev-button">
                        <img src="{$params.IMAGE_DIR}arrow_previous.png" alt="{t}Previous{/t}" /><br />
                        {t}Prev step{/t}
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <div class="content">

        {render_messages}

        <div class="alert alert-info">
            <button class="close" data-dismiss="alert">×</button>
            {t}Please select your desired persons to sent the newsletter to.{/t}
        </div>

        <div class="clearfix">
            <div class="pull-left" style="width:49%">
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

            <div class="pull-right" style="width:49%">
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
        </div>

        <input type="hidden" id="recipients_hidden" name="recipients" />
	</div>
</form>

{include file="newsletter/modals/_confirm_send.tpl"}
{/block}
