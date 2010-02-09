{* Botonera *} 
<div id="menu-acciones-admin">
	<div class="subtitle">
		Selección de artículos				
	</div>
	
	<div class="steps">
		<img src="{$params.IMAGE_DIR}newsletter/1.gif" width="300" height="40" border="0" usemap="#map" />
		{include file="newsletter/wizard.png.map"}
	</div>
	
	<ul>
	    <li>
            <a href="#" class="admin_add" title="Siguiente">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/next.png" alt="" /><br />
				Siguiente
            </a>
	    </li>
		
		<li class="separator"></li>
		
		<li>
			<a href="#" class="admin_add" title="Limpiar contenedor de noticias seleccionadas">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/editclear.png" alt="" /><br />
				Limpiar
            </a>
		</li>
		<li>
			<a href="#" class="admin_add" title="Seleccionar todas las noticias del contenedor superior">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/deselect.png" alt="" /><br />
				Seleccionar todos
            </a>
		</li>		
	</ul>
</div>

<div class="form">
	<form name="searchForm" id="searchForm" method="post" action="#">
		<input type="text" id="q" name="filters[q]" value="" />
		
		<select id="q_options" name="filters[options]">
			<option value="in_home">Home</option>
			<option value="frontpage">Portada</option>
			<option value="content_status">Hemeroteca</option>
		</select>
		
		<select id="q_category" name="filters[category]">
			<option value="-1">-- TODAS --</option>
			{foreach item="c_it" from=$content_categories}
				{if $c_it->pk_content_category!=4} {* != Opinion *}
					<option value="{$c_it->pk_content_category}">{$c_it->title}</option>
						
					{if count($c_it->childNodes)>0}                                    
						{foreach item="sc_it" from=$c_it->childNodes}
							<option value="{$sc_it->pk_content_category}">
						&nbsp; &rArr; {$sc_it->title}</option>
							
						{/foreach}
					{/if}
				{/if}
			{/foreach}
		</select>
		
		<button type="submit">Buscar</button>
		
		{* Valores asistente *}
		<input type="hidden" id="action"     name="action"     value="search" />
		<input type="hidden" id="source" 	 name="source"     value="Article" />
		<input type="hidden" id="postmaster" name="postmaster" value="" />
	</form>
</div>

<div id="container">
    <div id="container1">
		<h2>Listado de noticias </h2>
		<div class="info">(Doble click sobre la noticia para seleccionarla.)</div>
		
		{* Items searched *}
		<ul id="items-list"></ul>
	</div>
	
	<div id="separator"></div>
        
    <div id="container2">
		<h2>Noticias seleccionadas</h2>
		<div class="info">(Doble click sobre el titular para personalizar el texto.)</div>
		
		{* Items selected *}
		<ul id="items-selected"></ul>
	</div>
</div>


<script type="text/javascript">
/* <![CDATA[ */
var manager = null; // Newsletter.Manager
var searchEngine = null; // Newsletter.SearchEngine

var itemsList = {json_encode value=$items};
var postData  = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

{literal}
document.observe('dom:loaded', function() {
	var itemsSelected = new Array();
	if(postData!=null && postData.articles) {
		itemsSelected = postData.articles;
	}
	
	manager = new Newsletter.Manager('items-selected', {items: itemsSelected});		
	
	searchEngine = new Newsletter.SearchEngine('items-list', {
		'items': itemsList,
		'manager': manager,
		'form': 'searchForm'
	});
	
	$('postmaster').value = Object.toJSON(postData); // Binding post-data	
	
	var botonera = $$('div#menu-acciones-admin ul li a');
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
{/literal}
/* ]]> */
</script>
