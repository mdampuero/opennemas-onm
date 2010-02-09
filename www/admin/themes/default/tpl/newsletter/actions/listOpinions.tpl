{* Botonera *} 
<div id="menu-acciones-admin">
	<div class="subtitle">
		Selección de opiniones					
	</div>
	
	<div class="steps">
		<img src="{$params.IMAGE_DIR}newsletter/2.gif" width="300" height="40" border="0" usemap="#map" />
		{include file="newsletter/wizard.png.map"}
	</div>
	
	<ul>
	    <li>
            <a href="#" class="admin_add" title="Siguiente">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/next.png" alt="" /><br />
				Siguiente
            </a>
	    </li>
		<li>
            <a href="#" class="admin_add" title="Atrás">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/previous.png" alt="" /><br />
				Atrás
            </a>
	    </li>
		
		<li class="separator"></li>
		
		<li>
			<a href="#" class="admin_add" title="Limpiar contenedor de opiniones seleccionadas">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/editclear.png" alt="" /><br />
				Limpiar
            </a>
		</li>
		<li>
			<a href="#" class="admin_add" title="Seleccionar todas las opiniones del contenedor superior">
                <img border="0" src="{$params.IMAGE_DIR}newsletter/deselect.png" alt="" /><br />
				Seleccionar todas
            </a>
		</li>
	</ul>
</div>

<div class="form">
	<form name="searchForm" id="searchForm" method="post" action="#">
		<input type="text" id="q" name="filters[q]" value="" />
		
		<select id="q_options" name="filters[options]">
			<option value="in_home">Home</option>
			<option value="content_status">Hemeroteca</option>
		</select>
		
		<select id="q_author" name="filters[author]">
			<option value="-1">-- TODOS --</option>
			{section name="aut" loop=$authors}
				<option value="{$authors[aut]->pk_author}">{$authors[aut]->name}</option>					
			{/section}
		</select>
		
		<button type="submit">Buscar</button>
		
		{* Valores asistente *}
		<input type="hidden" id="action"     name="action"     value="search" />
		<input type="hidden" id="source" 	 name="source"     value="Opinion" />		
		<input type="hidden" id="postmaster" name="postmaster" value="" />
	</form>
</div>


<div id="container">
    <div id="container1">
		<h2>Listado de opiniones</h2>
		<div class="info">(Doble click sobre la opinión para seleccionarla.)</div>

		{* Items searched *}
		<ul id="items-list"></ul>
	</div>
	
	<div id="separator"></div>
        
    <div id="container2">
		<h2>Opiniones seleccionadas</h2>
		<div class="info">(Doble click sobre el titular para personalizar el texto.)</div>
		
		{* Items selected *}
		<ul id="items-selected"></ul>
	</div>
</div>


<script type="text/javascript">
var manager = null;
var searchEngine = null;

var itemsList = {json_encode value=$items};
var postData = {strip}{$smarty.request.postmaster|default:"null"}{/strip};

{literal}
document.observe('dom:loaded', function() {
    try {
        var itemsSelected = new Array();
        if(postData!=null && postData.opinions) {
            itemsSelected = postData.opinions;
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
{/literal}
</script>
