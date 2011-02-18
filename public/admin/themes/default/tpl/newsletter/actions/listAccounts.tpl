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
var manager = null;
var itemsList = {json_encode value=$items}; // Elementos para seleccionar
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

{literal}
document.observe('dom:loaded', function() {
	var itemsSelected = new Array();
	if(postData!=null && postData.accounts) {
		itemsSelected = postData.accounts;
	}

	manager = new Newsletter.AccountManager('items-list', 'items-selected', {
		items: itemsList,
		accounts: itemsSelected,
		form: 'searchForm'
	});

	$('postmaster').value = Object.toJSON(postData); // Binding post-data

    var botonera = $$('div#menu-acciones-admin ul li a');
    botonera[0].observe('click', function() {
		manager.serialize('accounts');

        $('searchForm').action.value = 'preview';
		$('searchForm').submit();
	});

    botonera[1].observe('click', function() {
        manager.serialize('accounts');

		$('searchForm').action.value = 'listOpinions';
		$('searchForm').submit();
	});

    botonera[2].observe('click', function() {
		manager.clearList();
	});

	botonera[3].observe('click', function() {
		manager.selectAll();
	});

    new Newsletter.UISplitPane('container', 'container1', 'container2', 'separator');

    $('map').select('area').each(function(tagArea) {
		tagArea.observe('click', function(evt) {
			Event.stop(evt);

			var attr = this.getAttribute('action');

			var form = $('searchForm');
			manager.serialize('accounts'); // global object

			form.action.value = attr;
			form.submit();
		});
	});
});
{/literal}
</script>

{/block}

{block name="content"}

<div class="wrapper-content" style="width:80%; margin:0 auto;">
	{* Botonera *}
	<div id="menu-acciones-admin" class="clearfix">
		<div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Newsletter management{/t}</h2></div>

		<div class="steps">
			<img src="{$params.IMAGE_DIR}newsletter/3.gif" width="300" height="40" border="0" usemap="#map" />
			{include file="newsletter/wizard.png.map"}
		</div>

		<ul>
			<li>
				<a href="#" class="admin_add" title="{t}Next{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/next.png" alt="" /><br />
					Siguiente
				</a>
			</li>

			<li>
				<a href="#" class="admin_add" title="{t}Back{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" alt="" /><br />
					Atrás
				</a>
			</li>

			<li class="separator"></li>

			<li>
				<a href="#" class="admin_add" title="{t}Clean selected elements from container{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/editclear.png" alt="" /><br />
					Limpiar
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" title="{t}Select all available elements{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/deselect.png" alt="" /><br />
					Seleccionar todos
				</a>
			</li>
		</ul>
	</div>

	<div class="form">
		<form name="searchForm" id="searchForm" method="post" action="#">
			{* Valores asistente *}
			<input type="hidden" id="action"     name="action"     value="preview" />
			<input type="hidden" id="postmaster" name="postmaster" value="" />
		</form>
	</div>

	<table class="adminheading" style="margin-top:30px;">
		<tr style="text-align:center;">
			<th nowrap>Subscriptores (pulse dos veces para incluir el subscriptor)</th>
			<th nowrap>Destinatarios seleccionados (pulse dos veces para eliminar un destinatario)</th>
		</tr>
	</table>
	<table class="adminlist" style="min-height:500px">
		<tr>
			<td width="50%">
				<div id="container1">
					<ul id="items-list" style="margin:0; padding:0"></ul>
				</div>
			</td>
			<td width="50%">
				<div id="container2">
					{* Items selected *}
					<ul id="items-selected" style="margin:0; padding:0"></ul>
				</div>
			</td>
		</tr>
	</table>


</div>
{/block}
