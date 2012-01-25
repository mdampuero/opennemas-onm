{extends file="base/admin.tpl"}

{block name="header-css" append}
{css_tag href="/admin.css"}
{css_tag href="/calendar_date_select.css"}
{css_tag href="/newsletter.css" media="screen"}
{/block}

{block name="footer-js" append}
{script_tag language="javascript" src="/newsletter.js"}
<script type="text/javascript">
var manager = null;
var itemsList = {json_encode value=$items}; // Elementos para seleccionar
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};
var mailList = {json_encode value=$mailList};

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

    manager3 = new Newsletter.AccountManager('items-list3', 'items-selected', {
		items: mailList,
		accounts: itemsSelected,
		form: 'searchForm'
	});

	$('postmaster').value = Object.toJSON(postData); // Binding post-data

    var botonera = $$('div#buttons ul li a');
    botonera[0].observe('click', function() {
		manager.serialize('accounts');
        manager.getTextarea('lists','othersMails');

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
    new Newsletter.UISplitPane('container', 'container3', 'container2', 'separator');

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
</script>

{/block}

{block name="content"}
<div id="buttons" class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title">
			<h2>{t}Newsletter management{/t}</h2>
			<img src="{$params.IMAGE_DIR}newsletter/3.gif" width="300" height="40" border="0" usemap="#map" />
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
				<a href="#" class="admin_add" title="{t}Back{/t}">
					<img border="0" src="{$params.IMAGE_DIR}arrow_previous.png" alt="" /><br />
					{t}Previous step{/t}
				</a>
			</li>

			<li>
				<a href="#" class="admin_add" title="{t}Clean container of selected opinions{/t}">
					<img border="0" src="{$params.IMAGE_DIR}editclear.png" alt="" /><br />
					{t}Clean{/t}
				</a>
			</li>
			<li>
				<a href="#" class="admin_add" title="{t}Select all the opinions available{/t}">
					<img border="0" src="{$params.IMAGE_DIR}newsletter/deselect.png" alt="" /><br />
					{t}Select all{/t}
				</a>
			</li>
            <li class="separator"></li>
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config newsletter module{/t}">
					<img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
					{t}Configurations{/t}
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="wrapper-content">

	<div class="form notice">
		<h3 style="margin:0 auto !important; padding:0 auto !important;">{t}Recipient selection{/t}</h3>
		{t}Please select your desired persons to sent the newsletter to.{/t}
	</div>

	<div class="form">
		<form name="searchForm" id="searchForm" method="post" action="#">
			{* Valores asistente *}
			<input type="hidden" id="action"     name="action"     value="preview" />
			<input type="hidden" id="postmaster" name="postmaster" value="" />
		</form>
	</div>

	<table class="adminheading">
		<tr style="text-align:center;font-size: 0.85em;">
			<th nowrap>{t}Subscriptors available (please double click over a subscritor to add to recipients){/t}</th>
			<th nowrap>{t}Subscriptors selected (please double click over a subscritor to delete from recipients){/t}</th>
		</tr>
	</table>
	<table class="adminlist" style="min-height:500px">
		<tr>
			<td width="50%">
                 <div id="container3"  style="min-height:50px;">
                     <label>{t}MailList Account{/t}: </label> <br />
					<ul id="items-list3" style="margin:0; padding:0px"></ul>
				</div>
                <hr>
				<div id="container1" >
                    <label>{t}DataBase Accounts{/t}:</label> <br />
					<ul id="items-list" style="margin:0; padding:0px"></ul>
				</div>
               
               
			</td>
			<td width="50%">
                <div id="container4" style="height:120px;">
                    <label>{t}Write others receivers{/t}</label> {t}(Separated by commas or different lines){/t} <br>
                    <textarea id="othersMails" name="othersMails" rows="5" cols="70"></textarea>
				</div>
                <hr>
				<div id="container2">
					{* Items selected *}
					<ul id="items-selected" style="margin:0; padding:0"></ul>
				</div>
			</td>
		</tr>
        <div id="separator"></div>
	</table>


</div>
{/block}
