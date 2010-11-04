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


<table class="adminheading" style="margin-top:30px;">
    <tr style="text-align:center;">
        <th nowrap>Listado de Opiniones (pulse dos veces para incluir un elemento)</th>
        <th nowrap>Opiniones seleccionadas (pulse dos veces para eliminar un elemento)</th>
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
