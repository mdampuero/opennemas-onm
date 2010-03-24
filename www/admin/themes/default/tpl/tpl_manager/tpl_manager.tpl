{include file="tpl_manager/header.tpl"}

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">
    
<div id="menu-acciones-admin">        
    <ul>
        <li>
			<a href="#delete" onclick="{literal}if(confirm('¿Está seguro de querer eliminar las cachés seleccionadas?')){sendForm('delete');}return false;{/literal}" title="Eliminar la caché">
				<img src="{$params.IMAGE_DIR}template_manager/delete48x48.png" border="0" /><br />
                Eliminar Caché
            </a>
		</li>
        
		<li>
			<a href="#refresh" rel="refresh" onclick="sendForm('refresh');return false;"
              title="Elimina caché y genera una nueva con datos actualizados">
				<img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" border="0" /><br />
                Renovar Caché
            </a>
		</li>		
		
        <li>
			<a href="#update" onclick="sendForm('update');return false;"
              title="Cambia fecha de expiración pero mantiene el contenido de la caché">
				<img src="{$params.IMAGE_DIR}template_manager/update48x48.png" border="0" /><br />
                Cambiar Expiración
            </a>
		</li>
        
        <li>
			<a href="{$smarty.server.SCRIPT_NAME}?action=config" title="Configurar cachés">
				<img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" border="0" /><br />
                Configuración
            </a>
		</li>
	</ul>
</div>


<div>
	
    <table class="adminheading">
        <tr>
            <td>
                
                <label>
                    Items/página:
                    <input type="text" name="items_page" id="items_page" value="{$smarty.request.items_page}"
                        size="2" maxlength="2" style="text-align: right;width: 1.5em;" />
                </label>
                
                <select name="type" id="type">
                    <option value="" {if $smarty.request.type eq ''}selected="selected"{/if}>Todos los tipos</option>
                    
                    <option value="frontpages" {if $smarty.request.type eq 'frontpages'}selected="selected"{/if}>Portadas</option>
                    
                    <option value="articles" {if $smarty.request.type eq 'articles'}selected="selected"{/if}>Noticia interior</option>
                    
                    <option value="opinions" {if $smarty.request.type eq 'opinions'}selected="selected"{/if}>Opinión interior</option>
                    
                    <option value="mobilepages" {if $smarty.request.type eq 'mobilepages'}selected="selected"{/if}>
                        Portadas versión móvil</option>
                        
                    <option value="rss" {if $smarty.request.type eq 'rss'}selected="selected"{/if}>RSS</option>                
                </select>
                
                <select name="section" id="section">
                    <option value="">Todas las secciones</option>
                    {html_options options=$sections selected=$smarty.request.section}
                </select>                        
                
                <button onclick="javascript:paginate(1);return false;">                
                    Actualizar
                    <img src="{$params.IMAGE_DIR}template_manager/reload16x16.png" border="0" align="absmiddle" />
                </button>
                
            </td>
        </tr>
    </table>

    {if count($caches)>0}
	<table id="tabla" name="tabla" width="100%" class="tabla">
    <thead>
        <tr>
			<th><input type="checkbox" value="" onclick="selectAll(this.checked, $('tabla').select('tbody input[type=checkbox]'));" /></th>
            <th>Recurso</th>
            <th>Categoría</th>
			<th>Fecha expiración</th>
            <th>Fecha creación</th>
			<th>Tamaño</th>                        
            <th>&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        {section name="c" loop=$caches}
        <tr bgcolor="{cycle values="#EEEEEE,#EEFFEE"}">
			<td width="16">
				<input type="checkbox" name="selected[]" value="{$smarty.section.c.index}" />
				<input type="hidden"   name="cacheid[]"  value="{$caches[c].category}|{$caches[c].resource}" />
                <input type="hidden"   name="tpl[]"      value="{$caches[c].template}.tpl" />                                
			</td>			
            <td>
                {* $titles[ $caches[c].resource ] *}
                {assign var="resource" value=$caches[c].resource}                
                
				{if isset($titles.$resource) && ($caches[c].template == 'article')}
                    <img src="{$params.IMAGE_DIR}template_manager/article16x16.png" border="0" title="Caché de artículo interior" />
                    <a href="{$smarty.const.URL_PUBLIC}article.php?article_id={$caches[c].resource}&action=read&category_name={$caches[c].category}"
                        style="text-decoration: underline;" target="_blank">
                        {$titles.$resource|clearslash}</a>
                        
                {elseif isset($titles.$resource) && ($caches[c].template == 'opinion')}
                    <img src="{$params.IMAGE_DIR}template_manager/opinion16x16.png" border="0" title="Caché de opinión interior" />
                    <a href="{$smarty.const.URL_PUBLIC}opinion.php?category_name=opinion&opinion_id={$caches[c].resource}&action=read"
                        style="text-decoration: underline;" target="_blank">
                        {$titles.$resource|clearslash}</a>
                        
                {elseif isset($authors.$resource)}
                    <img src="{$params.IMAGE_DIR}template_manager/rss16x16.png" border="0" title="Caché RSS - autor de opinión" />
                    <a href="{$smarty.const.URL_PUBLIC}rss/opinion/{$resource|replace:"RSS":""}/"
                        style="text-decoration: underline;" target="_blank">
                        {$authors.$resource|clearslash}</a>
                        
				{elseif $resource eq "RSS"}
                    <img src="{$params.IMAGE_DIR}template_manager/rss16x16.png" border="0" title="Caché RSS" />
                    {if $caches[c].category != 'home'}
                        <a href="{$smarty.const.URL_PUBLIC}rss/{$caches[c].category}/"
                            style="text-decoration: underline;" target="_blank">
                    {else}
                        <a href="{$smarty.const.URL_PUBLIC}rss/"
                            style="text-decoration: underline;" target="_blank">
                    {/if}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}</a>
                
                {elseif not isset($titles.$resource) && not isset($authors.$resource) && ($caches[c].template == 'mobile.index')}
                    <img src="{$params.IMAGE_DIR}template_manager/phone16x16.png" border="0" title="Caché de portadas versión móvil" />
                    <a href="{$smarty.const.URL_PUBLIC}mobile/seccion/{$caches[c].category}/"
                        style="text-decoration: underline;" target="_blank">
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}</a>
                    
                {elseif not isset($titles.$resource) && not isset($authors.$resource)}
                    <img src="{$params.IMAGE_DIR}template_manager/home16x16.png" border="0" title="Caché de portadas sección" />
                    <a href="{$smarty.const.URL_PUBLIC}seccion/{$caches[c].category}/{$caches[c].resource}"
                        style="text-decoration: underline;" target="_blank">
					{if $caches[c].resource gt 0}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"} (Pág. {$caches[c].resource})</a>
                    {else}
                        {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}</a>
                    {/if}
                {/if}
            </td>            
			<td width="270">
                {$ccm->get_title($caches[c].category)|clearslash|default:"PORTADA"}
            </td>
			<td width="170">
                {if $caches[c].expires < $smarty.now}
                    <img src="{$params.IMAGE_DIR}template_manager/outtime16x16.png" border="0" alt="La caché ya expiró" style="float: left; margin-right: 4px;" />
                {else}
                    <img src="{$params.IMAGE_DIR}template_manager/ok16x16.png" border="0" alt="Caché activa"  style="float: left; margin-right: 4px;" />
                {/if}
                <input type="text" name="expires[]" value="{$caches[c].expires|date_format:"%H:%M %d/%m/%Y"}"
                   maxlength="20" style="width: 130px;"/>                                
            </td>			
			<td width="125" align="center">
                {$caches[c].created|date_format:"%H:%M:%S %d/%m/%Y"} 
            </td>			
            <td width="70" align="right">
                {$caches[c].size/1000|string_format:"%d"} KB
            </td>            
			<td width="20">
                <a href="?action=refresh&amp;cacheid={$caches[c].category}|{$caches[c].resource}&amp;tpl={$caches[c].template}.tpl&{$paramsUri}"
                   title="Regenerar la caché">
                    <img src="{$params.IMAGE_DIR}template_manager/refresh16x16.png" border="0" alt="" /></a>
            </td>
            <td width="20">
                <a href="?action=delete&amp;cacheid={$caches[c].category}|{$caches[c].resource}&amp;tpl={$caches[c].template}.tpl&{$paramsUri}"
                    title="Eliminar la caché">
                    <img src="{$params.IMAGE_DIR}template_manager/delete16x16.png" border="0" alt="" /></a>
            </td>
        </tr>
        {/section}
    </tbody>
    
    <tfoot>
        <tr>
            <td colspan="8" align="center">
                <script language="javascript" type="text/javascript">
                // <![CDATA[
                {literal}
                function paginate(page) {                    
                    $('page').value = page;                    
                    $('formulario').submit();                
                }
                {/literal}
                // ]]>
                </script>
                {$pager->links}
            </td>            
        </tr>
    </tfoot>
    </table>
    {else}
        <h1>Ninguna caché disponible</h1>
    {/if}
	
</div>

<input type="hidden" id="page"   name="page"   value="{$smarty.request.page|default:'1'}" />	
<input type="hidden" id="action" name="action" value="" />
</form>

<script type="text/javascript">
{literal}
var previousValue = null;
document.observe('dom:loaded', function() {
    if( $('tabla') ) {
        $('tabla').select('tbody input[type=text]').each(function(item) {
            item.observe('change', function() {
                this.up(2).select('input[type=checkbox]')[0].
                    setAttribute('checked', 'checked');
            });
            
            new Control.DatePicker(item,{
                icon: './themes/default/images/template_manager/update16x16.png',
                locale: 'es_ES',
                timePicker: true,
                timePickerAdjacent: true,
                onSelect: function(fecha, instance) {
                    instance.element.up(2).select('input[type=checkbox]')[0].
                        setAttribute('checked', 'checked'); 
                },
                onHover: function(fecha, instance) {
                    instance.element.up(2).select('input[type=checkbox]')[0].
                        setAttribute('checked', 'checked'); 
                }
            });
        });
    }
    
    $$('a[rel=refresh]').each(function(item) {
        item.observe('mouseover', function() {
            $('adviceRefresh').setStyle({display: ''});
        });
        
        item.observe('mouseout', function() {
            $('adviceRefresh').setStyle({display: 'none'});
        });
    });
});

function sendForm(actionValue) {
	// FIXME: chequeos de seguridad
	$('action').value = actionValue;
	$('formulario').submit();
}

function selectAll(indicator, checkboxes) {	
	for(var i=0; i<checkboxes.length; i++) {
		if(indicator) {
			checkboxes[i].setAttribute('checked', 'checked');
			checkboxes[i].selected = true;
		} else {
			checkboxes[i].removeAttribute('checked');
			checkboxes[i].selected = false;
		}
	}
}
{/literal}
</script>

{include file="footer.tpl"}