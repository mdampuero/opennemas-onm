{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/newsletter.css" media="screen"}
<style type="text/css">
    .btn-group .btn {
        display:inline-block;
    }
    #accounts-provider > div {
        min-height:400px;
    }
    #accounts-provider ul {
        margin-top:10px;
        display:block;
    }

    #accounts-provider ul li {
        padding:3px;
        border:1px solid #ccc;
    }
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}
{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>

    <div id="buttons-recipients" class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Newsletter management{/t} :: {t}Recipient selection{/t}</h2>
            </div>

            <ul class="old-button">

                <li>
                    <a href="#" class="admin_add" title="{t}Next{/t}" id="next-button">
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

    <div class="wrapper-content">

        {render_messages}

        <div class="alert alert-info">
            <button class="close" data-dismiss="alert">×</button>
            {t}Please select your desired persons to sent the newsletter to.{/t}
        </div>

        <div class="clearfix">
            <div class="pull-left" style="width:49%">
                <div id="accounts-provider" class="tabs">
                    <ul>
                        <li><a href="#maillist-account">{t}MailList{/t}</a></li>
                        <li><a href="#database-accounts">{t}Database accounts{/t}</a></li>
                        <li><a href="#custom-accounts">{t}Custom{/t}</a></li>
                    </ul>

                    <div id="maillist-account">
                        <ul>
                            {foreach name=d from=$mailList item=mail}
                            <li  data-email="{$mail->email}"  data-name="{$mail->name}">
                                {$mail->name}:{$mail->email}
                            </li>
                            {/foreach}
                        </ul>
                    </div>

                    <div id="database-accounts">
                        <div class="btn-group pull-right">
                            <a id="button-check-all" href="#" class="btn">
                                <i class="icon-check"></i>{t}Select all{/t}
                            </a>
                            <a class="btn" id="add-selected" href="#">
                                <i class="icon-plus"></i>{t}Add selected{/t}
                            </a>
                        </div>
                        <ul>
                            {foreach name=d from=$accounts item=account}
                                <li  data-email="{$account->email}" data-name="{$account->name}">
                                    <label>
                                        <input type="checkbox" name="selected" class="">
                                        {$account->name}:{$account->email}
                                    </label>
                                </li>
                            {/foreach}
                        </ul>
                    </div>

                    <div id="custom-accounts" class="form-vertical">
                        <div class="control-group">
                            <div class="controls">
                                <textarea id="othersMails" name="othersMails" style="width:90%"></textarea>
                                <div class="help-block">{t}Write them separated by commas or in different lines.{/t}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pull-right" style="width:49%">
                <p>{t}Receivers{/t}</p>
                <div id="recipients" style="min-height:150px; border:1px solid Gray;">
                    <ul id="items-recipients" style="min-height:50px;margin:0; padding:0">
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

        <input type="hidden" id="action" name="action" value="send" />
	    <div id="separator"></div>
	</div>

</form>
{/block}
