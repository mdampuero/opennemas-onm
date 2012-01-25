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
var searchEngine = null;

var itemsList = {json_encode value=$items};
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};


document.observe('dom:loaded', function() {
    try {
        var itemsSelected = new Array();
        if(postData!=null && postData.opinions) {
            itemsSelected = postData.opinions;
        }

        manager = new Newsletter.Manager('items-selected', { items: itemsSelected });

        searchEngine = new Newsletter.SearchEngine('items-list', {
            'items': itemsList,
            'manager': manager,
            'form': 'searchForm'
        });

        $('postmaster').value = Object.toJSON(postData); // Binding post-data

        var botonera = $$('div#buttons ul li a');
        botonera[0].observe('click', function() {
            manager.serialize('opinions');

            $('searchForm').action.value = 'listAccounts';
            $('searchForm').submit();
        });

        botonera[1].observe('click', function() {
            manager.serialize('opinions');

            $('searchForm').action.value = 'listArticles';
            $('searchForm').submit();
        });

        botonera[2].observe('click', function() {
            manager.clearList();
        });

        botonera[3].observe('click', function() {
            searchEngine.selectAll();
        });

        new Newsletter.UISplitPane('container', 'container1', 'container2', 'separator');

        // Wizard icons step
        $('map').select('area').each(function(tagArea) {
            tagArea.observe('click', function(evt) {
                Event.stop(evt);

                var attr = this.getAttribute('action');

                var form = $('searchForm');
                manager.serialize('opinions'); // global object

                form.action.value = attr;
                form.submit();
            });
        });
    } catch(e) {
        console.log(e);
    }
});

</script>

{/block}

{block name="content"}
<div id="buttons" class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title">
			<h2>{t}Newsletter management{/t}</h2>
			<img src="{$params.IMAGE_DIR}newsletter/2.gif" width="300" height="40" border="0" usemap="#map" />
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
		<h3 style="margin:0 auto !important; padding:0 auto !important;">{t}Opinion selection{/t}</h3>
		Please select your desired opinions to attach in the newsletter.
	</div>

	<table class="adminheading">
		<tr>
			<td>
			<form name="searchForm" id="searchForm" method="post" action="#">
				{t}Opinions that contain {/t}
				<input type="text" id="q" name="filters[q]" value="" />
				{t}in{/t}
				<select id="q_options" name="filters[options]">
					<option value="in_home">{t}Home{/t}</option>
					<option value="content_status">{t}Library{/t}</option>
				</select>
				{t}from author{/t}
				<select id="q_author" name="filters[author]">
					<option value="-1">-- {t}ALL{/t} --</option>
					{section name="aut" loop=$authors}
						<option value="{$authors[aut]->pk_author}">{$authors[aut]->name}</option>
					{/section}
				</select>

				<button type="submit">{t}Search{/t}</button>

				{* Valores asistente *}
				<input type="hidden" id="action"     name="action"     value="search" />
				<input type="hidden" id="source" 	 name="source"     value="Opinion" />
				<input type="hidden" id="postmaster" name="postmaster" value="" />
			</form>
			</td>
		</tr>
	</table>
	<table class="adminlist" >
		<thead style="text-align:center;">
			<th nowrap>Listado de Opiniones (pulse dos veces para incluir un elemento)</th>
			<th nowrap>Opiniones seleccionadas (pulse dos veces para eliminar un elemento)</th>
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
