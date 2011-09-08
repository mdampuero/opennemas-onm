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
	/* <![CDATA[ */
	var manager = null; // Newsletter.Manager
	var searchEngine = null; // Newsletter.SearchEngine

	var itemsList = {json_encode value=$items};
	var postData  = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

	document.observe('dom:loaded', function() {
		var itemsSelected = new Array();
		if(postData!=null && postData.articles) {
			itemsSelected = postData.articles;
		}

		manager = new Newsletter.Manager('items-selected', { items: itemsSelected });

		searchEngine = new Newsletter.SearchEngine('items-list', {
			'items': itemsList,
			'manager': manager,
			'form': 'searchForm'
		});

		$('postmaster').value = Object.toJSON(postData); // Binding post-data

		var botonera = $('buttons').select('ul li a');
		botonera[0].observe('click', function() {
			manager.serialize('articles');

			searchEngine.form.action.value = 'listOpinions';
			searchEngine.form.submit();
		});

		botonera[1].observe('click', function() {
			manager.clearList();
		});

		botonera[2].observe('click', function() {
			searchEngine.selectAll();
		});

		new Newsletter.UISplitPane('container', 'container1', 'container2', 'separator');

		// Wizard icons step
		$('map').select('area').each(function(tagArea) {
			tagArea.observe('click', function(evt) {
				Event.stop(evt);

				var attr = this.getAttribute('action');

				var form = $('searchForm');
				manager.serialize('articles'); // global object

				form.action.value = attr;
				form.submit();
			});
		});
	});
	/* ]]> */
	</script>
{/block}

{block name="content"}

<div id="buttons" class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title">
			<h2>{t}Newsletter management{/t}</h2>
			<img src="{$params.IMAGE_DIR}newsletter/1.gif" width="300" height="40" border="0" usemap="#map" />
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

            <li >
                <a href="subscriptors.php?action=list" class="admin_add" name="submit_mult" value="{t}Subscriptors{/t} title="{t}Subscriptors{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}authors.png" title="{t}Subscriptors{/t}" alt="{t}Subscriptors{/t}"><br />{t}Subscriptors{/t}
                </a>
            </li>
		</ul>

	</div>
</div>


<div class="wrapper-content">

	{render_messages}

	<div class="form notice">
		<h3 style="margin:0 auto !important; padding:0 auto !important;">{t}Article selection{/t}</h3>
		Please select your desired articles to attach in the newsletter.
	</div>

	<table class="adminheading">
		<tr>
			<td>
				<form name="searchForm" id="searchForm" method="post" action="#">
					{t}Articles that contain {/t}
					<input type="text" id="q" name="filters[q]" value="" />
					{t}in{/t}
					<select id="q_options" name="filters[options]">
						<option value="in_home">{t}Home{/t}</option>
						<option value="frontpage">{t}Frontpage{/t}</option>
						<option value="content_status">{t}Library{/t}</option>
					</select>
					{t}from category{/t}
					<select id="q_category" name="filters[category]">
						<option value="-1">-- {t}ALL{/t} --</option>
						{foreach item="c_it" from=$content_categories}
							{if $c_it->pk_content_category!=4} {* != Opinion *}
								<option value="{$c_it->pk_content_category}">{$c_it->title}</option>
								{if count($c_it->childNodes)>0}
									{foreach item="sc_it" from=$c_it->childNodes}
										<option value="{$sc_it->pk_content_category}">&nbsp; &rArr; {$sc_it->title}</option>

									{/foreach}
								{/if}
							{/if}
						{/foreach}
					</select>

					<button type="submit">{t}Buscar{/t}</button>

					{* Valores asistente *}
					<input type="hidden" id="action"     name="action"     value="search" />
					<input type="hidden" id="source" 	 name="source"     value="Article" />
					<input type="hidden" id="postmaster" name="postmaster" value="" />
				</form>

			</td>
		</tr>
	</table>
	<table class="adminlist" >
		<thead style="text-align:center;">
			<th nowrap>{t}Available articles (please, double click over one element to send it){/t}</th>
			<th nowrap>{t}Selected articles (please doucle click over one element to discart it){/t}</th>
		</thead>
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
        <div id="separator"></div>
	</table>

</div>
{/block}
