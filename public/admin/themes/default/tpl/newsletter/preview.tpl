{extends file="base/admin.tpl"}

{block name="header-css" append}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}calendar_date_select.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}newsletter.css" media="screen" />
{/block}

{block name="footer-js" append}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}newsletter.js?cacheburst=1259855452"></script>
<script type="text/javascript">
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};


document.observe('dom:loaded', function() {
    // Set postmaster value
    $('postmaster').value = Object.toJSON(postData);

    // Attach click event to button
	var botonera = $('buttons').select('ul li a');
    botonera[0].observe('click', function() {
		$('searchForm').action.value = 'send';
		$('searchForm').submit();
	});

    botonera[1].observe('click', function() {
		$('searchForm').action.value = 'listAccounts';
		$('searchForm').submit();
	});

    $('map').select('area').each(function(tagArea) {
		tagArea.observe('click', function(evt) {
			Event.stop(evt);

			var attr = this.getAttribute('action');
			var form = $('searchForm');

            form.action.value = attr;
			form.submit();
		});
	});
});

</script>

{/block}

{block name="content"}
<div id="buttons" class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title">
			<h2>{t}Newsletter management{/t}</h2>
			<img src="{$params.IMAGE_DIR}newsletter/4.gif" width="300" height="40" border="0" usemap="#map" />
			{include file="newsletter/_partials/wizard.png.map"}
		</div>
		<ul class="old-button">
			<li>
				<a href="#" class="admin_add" title="{t}Next{/t}">
					<img border="0" src="{$params.IMAGE_DIR}arrow_next.png" alt="" /><br />
					{t}Next step{/t}
				</a>
			</li>

			<li>
				<a href="#" class="admin_add" title="{t}Previous{/t}">
					<img border="0" src="{$params.IMAGE_DIR}arrow_previous.png" alt="" /><br />
					{t}Previous step{/t}
				</a>
			</li>

		</ul>
	</div>
</div>
<div class="wrapper-content">

	<table class="adminheading">
		<th>
			<td></td>
		</th>
	</table>

	<table class="adminlist">
		<tr>
			<td>
				<div  style="width:80%; margin:0 auto;">
					<form name="searchForm" id="searchForm" method="post" action="#">
						<p>
							<label>{t}Email subject{/t}:</label>
							<input type="text" name="subject" id="subject" size="80"
								   value="[{$smarty.const.SITE_FULLNAME}] Boletín de noticias {$smarty.now|date_format:"%d/%m/%Y"}" />
						</p>

						{* Valores asistente *}
						<input type="hidden" id="action"     name="action"     value="send" />
						<input type="hidden" id="postmaster" name="postmaster" value="" />
					</form>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div style="width:75%; margin:0 auto;">
					<div style="border:1px solid #ccc; padding:20px;">
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




		</div>
	</div>


</div>
{/block}
