{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
#htmlContent {
    border:1px solid #ccc;
    padding:10px;
    background-color: white;
    max-width:100%;
    display:block;
}
#html-content-textarea {
    display:block;
    width:100%;
    display:none;
    min-height:500px;
}
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/onm/newsletter.js"}
<script type="text/javascript">
$(document).data('saved', true);
var  newsletter_urls = {
    save_contents : '{url name=admin_newsletter_save_html id=$newsletter->id}'
}
</script>
{/block}

{block name="content"}

<form action="{url name=admin_newsletter_save_html id=$newsletter->id}" method="POST" id="newsletter-preview-form">

<div id="buttons-preview" class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title">
            <h2>{t}Newsletter{/t} :: {t}Preview{/t}</h2>
        </div>

		<ul class="old-button">
			<li>
				<a href="{url name=admin_newsletter_pick_recipients id=$newsletter->id}" class="admin_add" title="{t}Next{/t}" id="next-button">
					<img src="{$params.IMAGE_DIR}arrow_next.png" alt="{t}Next{/t}" /><br />
					{t}Next step{/t}
				</a>
			</li>

			<li>
				<a href="{url name=admin_newsletter_show_contents id=$newsletter->id}" class="admin_add" title="{t}Previous{/t}" id="prev-button">
					<img src="{$params.IMAGE_DIR}arrow_previous.png" alt="{t}Previous{/t}" /><br />
					{t}Previous step{/t}
				</a>
			</li>
			<li>
				<a href="#" title="{t}Edit{/t}" id="edit-button">
					<img src="{$params.IMAGE_DIR}edit.png" alt="{t}Edit{/t}" /><br />
					{t}Edit{/t}
				</a>
			</li>
			<li id="li-save-button" style="display:none;">
                <a id="save-button" href="#" class="admin_add" title="{t}Save changes{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}save.png" alt="{t}Save changes{/t}" ><br />{t}Save changes{/t}
                </a>
            </li>
            <li class="separator"></li>
            <li>
                <a href="{url name=admin_newsletters}" class="admin_add" title="{t}Back to list{/t}">
                    <img src="{$params.IMAGE_DIR}previous.png" alt="" /><br />
                    {t}Back to list{/t}
                </a>
            </li>


		</ul>
	</div>
</div>
<div class="wrapper-content">
    <div class="form-horizontal panel">
        <div class="control-group">
            <label for="name" class="control-label">{t}Email subject{/t}</label>
            <div class="controls">
                <input type="text" name="subject" id="title" value="{$newsletter->title}" required class="input-xxlarge"/>
            </div>
        </div>

        <div class="control-group">
            <label for="htmlContent" class="control-label">{t}Preview{/t}</label>
            <div class="controls" >
                <div id="html_content">{$newsletter->html}</div>
            </div>
        </div>
    </div>
</form>
{include file="newsletter/modals/_save_changes_alert.tpl"}
{/block}
