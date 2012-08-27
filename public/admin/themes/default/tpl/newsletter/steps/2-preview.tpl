{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/newsletter.css" media="screen"}
<style type="text/css">
#htmlContent {
    border:1px solid #ccc;
    padding:20px;
    background-color: white;
    max-width:80%;
    margin:0 auto;
    display:block;
}
</style>
{/block}

{block name="footer-js" append}
{script_tag src="/jquery/jquery.cookie.js"}
{script_tag src="/jquery-onm/jquery.newsletter.js"}
{script_tag src="/tiny_mce/opennemas-config.js"}
<script type="text/javascript">
//TinyMce scripts
tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
OpenNeMas.tinyMceConfig.advanced.elements = "htmlContent";
</script>
{/block}

{block name="content"}

<form action="{url name=admin_newsletter_save_html id=$newsletter->id}" method="POST">

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
    <div class="form-vertical panel">
        <div class="control-group">
            <label for="name" class="control-label">{t}Email subject{/t}</label>
            <div class="controls">
                <input type="text" name="subject" id="subject" style="width:80%" value="{$newsletter->subject}" required class="input-xlarge"/>
            </div>
        </div>

        <div class="control-group">
            <label for="htmlContent" class="control-label">{t}Preview{/t}</label>
            <div class="controls">
                <div id="htmlContent">{$newsletter->html}</div>
            </div>
        </div>
    </div>
</form>

{include file="newsletter/modals/_back_contents_accept.tpl"}

{/block}
