{extends file="base/admin.tpl"}

{block name="header-css" append}
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}calendar_date_select.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}newsletter.css" media="screen" />
{/block}

{block name="footer-js" append}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}newsletter.js?cacheburst=1259855452"></script>
<script type="text/javascript">
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

{literal}
document.observe('dom:loaded', function() {
    // Set postmaster value
    $('postmaster').value = Object.toJSON(postData);

    // Attach click event to button
	var botonera = $$('div#menu-acciones-admin ul li a');
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
{/literal}
</script>

{/block}

{block name="content"}
<div class="wrapper-content">

	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Newsletter management{/t}</h2></div>

		<div class="steps">
			<img src="{$params.IMAGE_DIR}newsletter/4.gif" width="300" height="40" border="0" usemap="#map" />
			{include file="newsletter/wizard.png.map"}
		</div>

		<ul>
			<li>
				<a href="#" class="admin_add" title="{t}Next{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/next.png" alt="" /><br />
					{t}Next{/t}
				</a>
			</li>

			<li>
				<a href="#" class="admin_add" title="{t}Previous{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" alt="" /><br />
					{t}Previous{/t}
				</a>
			</li>

		</ul>
	</div>

	<div class="margin:0 auto; width:70%; text-align:center">
		<div class="form"  style="margin:0 auto; width:80%; text-align:left">
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

			<p>
				<label>{t}Newsletter preview:{/t}</label>
			</p>
			<div id="preview">
				{$htmlContent}
			</div>

		</div>
	</div>


</div>
{/block}
