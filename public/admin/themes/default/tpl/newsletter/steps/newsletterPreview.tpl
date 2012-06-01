{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/newsletter.css" media="screen"}
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

<form action="#" method="post" name="newsletterForm" id="newsletterForm" {$formAttrs}>

<div id="buttons-preview" class="top-action-bar clearfix">
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
					{t}Previous step{/t}
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" title="{t}Edit{/t}" id="edit-button">
					<img src="{$params.IMAGE_DIR}edit.png" alt="{t}Edit{/t}" /><br />
					{t}Edit{/t}
				</a>
			</li>


		</ul>
	</div>
</div>
<div class="wrapper-content">
	<table class="adminheading">
        <tr>
            <th>
                {t}Newsletter preview{/t}
            </th>
        </tr>
	</table>
	<table class="adminlist">
		<tr>
			<td>
				<div  style="width:80%; margin:0 auto;">
						<p>
							<label>{t}Email subject{/t}:</label>
							<input type="text" name="subject" id="subject" style="width:80%"
								   value="{setting name="site_name"} [{$smarty.now|date_format:"%d/%m/%Y"}]" />
						</p>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:85%; margin:0 auto;">
					<div id="htmlContent" style="border:1px solid #ccc; padding:20px;background-color: white;">
						{$htmlContent}
					</div>
					<br>
				</div>
			</td>
		</tr>
		<tfoot>
			<tr>
				<td></td>
			</tr>
		</tfoot>
	</table>

        <input type="hidden" id="action" name="action" value="listRecipients" />
	    <div id="separator"></div>
	</div>

</form>

{include file="newsletter/modals/_back_contents_accept.tpl"}

{/block}
