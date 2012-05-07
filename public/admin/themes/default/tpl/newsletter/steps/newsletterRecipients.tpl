{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/jquery-onm/newsletter/jquery.stepRecipients.js"}
{/block}

{block name="content"}

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>

    <div id="buttons" class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Newsletter management{/t}</h2>
            </div>

            <ul class="old-button">

                <li>
                    <a href="#" class="admin_add" title="{t}Next{/t}" id="next-button">
                        <img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
                        {t}Next step{/t}
                    </a>
                </li>
                 <li>
                    <a href="#" class="admin_add" title="{t}Previous{/t}" id="prev-button">
                        <img src="{$params.IMAGE_DIR}arrow_previous.png" alt="{t}Previous{/t}" /><br />
                        {t}Prev step{/t}
                    </a>
                </li>

                <li>
                    <a href="#" class="admin_add" title="{t}Clean containers{/t}" id="clean-button">
                        <img src="{$params.IMAGE_DIR}editclear.png" alt="{t}Clean containers{/t}" /><br />
                        {t}Clean{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config newsletter module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Configurations{/t}
                    </a>
                </li>

                <li >
                    <a href="subscriptors.php?action=list" class="admin_add" id="submit_mult" title="{t}Subscriptors{/t}">
                        <img src="{$params.IMAGE_DIR}authors.png" title="{t}Subscriptors{/t}" alt="{t}Subscriptors{/t}"><br />{t}Subscriptors{/t}
                    </a>
                </li>
            </ul>

        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

		<div class="form notice">
			<h3 style="margin:0 auto !important; padding:0 auto !important;">{t}Recipient selection{/t}</h3>
			{t}Please select your desired persons to sent the newsletter to.{/t}
		</div>

		<table class="adminheading">
			<tr style="text-align:center;font-size: 0.85em;">
				<th>{t}Subscriptors available (please double click over a subscritor to add to recipients){/t}</th>
				<th>{t}Subscriptors selected (please double click over a subscritor to delete from recipients){/t}</th>
			</tr>
		</table>
		<table class="adminlist" style="min-height:500px">
			<tr class="noHover">
				<td style="width:50%">
	                 <div id="mailList"  style="min-height:50px;">
	                    <label>{t}MailList Account{/t}: </label> <br />
						<ul id="items-mailList" style="margin:0; padding:0px">
							{section name=d loop=$mailList}
                            <li  data-email="{$mailList[d]->email}"  data-name="{$mailList[d]->name}">
                                {$mailList[d]->name}:{$mailList[d]->email}
                            </li>
                        	{/section}
						</ul>
					</div>
	                <hr>
					<div id="dbList" >
	                    <label>{t}DataBase Accounts{/t}:</label> <br />
						<ul id="items-dbList" style="margin:0; padding:0px">
							{section name=d loop=$accounts}
                            <li  data-email="{$accounts[d]->email}" data-name="{$accounts[d]->name}">
                                {$accounts[d]->name}:{$accounts[d]->email}
                            </li>
                        {/section}
						</ul>
					</div>


				</td>
				<td style="width:50%">
	                <div id="manualList" style="padding:4px;height:120px;">
	                    <label>{t}Write others receivers{/t}</label> {t}(Separated by commas or different lines){/t} <br>
	                    <textarea id="othersMails" name="othersMails" style="width:90%"></textarea>
					</div>
	                <hr>
					<div id="recipients" style="height:150px;">
						<ul id="items-recipients" style="min-height:50px;margin:0; padding:0">
                            {if !empty($recipients)}
                            {section name=d loop=$recipients}
                            <li  data-email="{$recipients[d]->email}"  data-name="{$recipients[d]->name}">
                                {$recipients[d]->name}:{$recipients[d]->email}
                            </li>
                            {/section}
                            {/if}
                        </ul>
					</div>
				</td>
			</tr>
		</table>
        <textarea name="newsletter" id="newsletter" style="display:;"></textarea>
        <textarea name="recipients" id="recipients" style="display:;"></textarea>
        <input type="hidden" id="action" name="action" value="send" />
	    <div id="separator"></div>
	</div>

</form>
{/block}
