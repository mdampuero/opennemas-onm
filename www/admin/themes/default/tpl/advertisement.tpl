    {* ******************************************************************************* *}
{include file="header.tpl"}

{*
1- big superior izq
2- big superior derecha
3 banner cabecera
4 banner flotante derecho
5 botoncuadrado columna
6 lateral derecho1
7 separador horizontal
8 mini1 dcha
9 mini 2 dcha
10 Banner Inferior Izquierda 1
11 Banner Inferior columna 1
12 Banner Inferior Izquierda 2 
13 Banner Inferior columna 2
14 botoncuadrado columna2
15 lateral derecho2
16 botoncuadrado columna2

101 banner noticia interior
102 banner columna interior 1
103 banner columna interior 2
*}

{* ******************************************************************************* *}
{* LISTADO *********************************************************************** *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}

<table border=0 cellpadding=0 cellspacing=0><tr><td valign='top' align='right'>
<ul class="tabs">
<li>
	<a href="advertisement.php?action=list&category=0" {if $category==0 } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>HOME</font></a>
	</li>
	<li>
	<a href="advertisement.php?action=list&category=4" {if $category==4 } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>OPINIÓN</font></a>
	</li>
		<li>
	<a href="advertisement.php?action=list&category=9" {if $category==9 } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>CONECTA</font></a>
	</li>
</ul>
</td><td>
{include file="menu_categorys.tpl" home="advertisement.php?action=list"}
</td></tr></table>

<div id="{$category}">

{include file="botonera_up.tpl"}

<script type="text/javascript">
{literal}
function submitFilters(frm) {
    $('action').value='list';
    $('page').value = 1;
    
    frm.submit();
}
{/literal}
</script>

<table class="adminheading">
	<tr>
		<th nowrap="nowrap" align="right">
            <label>Tipo de banner:
            <select name="filter[type_advertisement]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.type_advertisement
                              selected=$smarty.request.filter.type_advertisement} 
            </select></label>
            &nbsp;&nbsp;&nbsp;

            <label>Estado:
            <select name="filter[available]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.available
                              selected=$smarty.request.filter.available} 
            </select></label>
            &nbsp;&nbsp;&nbsp;
            
            <label>Tipo:
            <select name="filter[type]" onchange="submitFilters(this.form);">
                {html_options options=$filter_options.type
                              selected=$smarty.request.filter.type} 
            </select></label>            
            
            {* $_REQUEST['page'] => $_POST['page'] is more important that $_GET['page'], see also php.ini - variables_order *}
            <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:"1"}" />
        </th>
	</tr>	
</table>

<table class="adminlist">
<thead>
<tr>
    <th></th>
    <th class="title">Tipo</th>
    <th>Título</th>
    <th align="center">Permanencia</th>
    <th align="center">Clicks</th>
    <th align="center">Visto</th>    
    <th align="center">Tipo</th>
    <th align="center">Publicado</th>
    <th align="center">Modificar</th>
    <th align="center">Eliminar</th>
</tr>
</thead>

<tbody>
{section name=c loop=$advertisements}
<tr {cycle values="class=row0,class=row1"}>
    <td style="text-align:center;font-size: 11px;width:5%;">
        <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
            value="{$advertisements[c]->pk_advertisement}" />
    </td>
	<td style="font-size: 11px;">
        <label for="title">
            {assign var="type_advertisement" value=$advertisements[c]->type_advertisement}            
            {$map.$type_advertisement}
        </label>            
	</td>
	<td style="font-size: 11px;">
		{$advertisements[c]->title|clearslash}
	</td>

    <td style="text-align:center;font-size: 11px;width:80px;" align="center">
		{if $advertisements[c]->type_medida == 'NULL'} Indefinida {/if} 
		{if $advertisements[c]->type_medida == 'CLIC'} Clicks: {$advertisements[c]->num_clic} {/if} 
		{if $advertisements[c]->type_medida == 'VIEW'} Visionados: {$advertisements[c]->num_view} {/if} 
		{if $advertisements[c]->type_medida == 'DATE'}
            Fecha: {$advertisements[c]->starttime|date_format:"%d:%m:%Y"}-{$advertisements[c]->endtime|date_format:"%d:%m:%Y"}
        {/if}			
	</td>
    
	<td style="text-align:center;font-size: 11px;width:105px;" align="right">
		{$advertisements[c]->num_clic_count} 
	</td>
	<td style="text-align:center;font-size: 11px;width:40px;" align="right">
		 {$advertisements[c]->views}  
	</td>
    <td style="text-align:center;font-size: 11px;width:70px;" align="center">
        {if $advertisements[c]->with_script == 1}
            <img src="{$params.IMAGE_DIR}iconos/script_code_red.png" border="0"
                 alt="Javascript" title="Javascript" />            
        {else}
            <img src="{$params.IMAGE_DIR}iconos/picture.png" border="0" alt="Multimedia"
                 title="Elemento multimedia (flash, imagen, gif animado)" />
        {/if}
    </td>
	<td style="text-align:center;width:70px;" align="center">
		{if $advertisements[c]->available == 1}
			<a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;category={$category}&amp;status=0&amp;&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                title="Publicado">
				<img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
		{else}
			<a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                title="Pendiente">
				<img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
		{/if}
	</td>

	<td style="text-align:center;width:70px;" align="center">
		<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$advertisements[c]->id}');" title="Modificar">
			<img src="{php}echo($this->image_dir);{/php}edit.png" border="0" /></a>
	</td>

	<td style="text-align:center;width:70px;" align="center">
		<a href="#" onClick="javascript:confirmar(this, '{$advertisements[c]->id}');" title="Eliminar">
			<img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
	</td>

</tr>
{sectionelse}
<tr>
	<td align="center" colspan="10">
        <h2>Ninguna publicidad guardada en esta sección</h2>
    </td>
</tr>
{/section}
</tbody>

<tfoot>
{if count($advertisements) gt 0}
<tr>
    <td colspan="10" style="padding:10px;font-size: 12px;" align="center">
        <br /><br />
        {$paginacion->links}
        <br /><br />
    </td>
</tr>
{/if}
</tfoot>

</table>
{/if}



{* ******************************************************************************* *}
{* FORMULARIO PARA ENGADIR UN CONTENIDO PUBLICIDAD******************************** *}
{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

{literal}
<style>
#tabs a {
    background-color: #F5F5F5;
}

#tabs a.active-tab {
    background-color: #EEE;
    font-weight: bold;
}
</style>

<script language="javascript" type="text/javascript">
function testScript(frm)  {
    frm.action.value = 'test_script';
    frm.target = 'test_script'; // abrir noutra ventá
    frm.submit();
    
    frm.target = ''; // cambiar o target para que siga facendo peticións na mesma ventá
    frm.action.value = '';
}
</script>
{/literal}

{include file="botonera_up.tpl"} 

<table border="0" cellpadding="2" cellspacing="2" class="fuente_cuerpo" width="800">
<tbody>
    
<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" width="30%">
		<label for="title">Nombre:</label>
	</td>
	<td valign="top" style="height: 20px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Publicidad"
			size="80" value="{$advertisement->title|clearslash|escape:"html"}" class="required" onBlur="javascript:get_metadata(this.value);"/>
		{* <input type="hidden" id="category" name="category" title="Publicidad" value="advertisement" /> *}
	</td>
    
    {* begin: PANEL PROPIEDADES *}
    <td rowspan="5" valign="top">
        <b class="rtop"><b class="r1"></b><b class="r2"></b><b class="r3"></b><b class="r4"></b></b>
        <div style="background-color: #F5F5F5; padding: 18px 9px; width: 400px;">            
            <table width="100%" border="0">
                <tr>
                    <td valign="top" align="right"><label for="available">Publicado:</label></td>
                    <td>
                        <select name="available" id="available">
                            <option value="1" {if $advertisement->available == 1}selected="selected"{/if}>Si</option>
                            <option value="0" {if $advertisement->available == 0}selected="selected"{/if}>No</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <td valign="top" align="right">
                        <label for="metadata">Palabras clave: </label><br />
                        <sub>Separadas por comas</sub>
                    </td>
                    <td>
                        <textarea id="metadata" name="metadata" cols="20" rows="2" style="width: 100%;"
                           title="Metadatos" value="">{$advertisement->metadata|strip}</textarea>                        
                    </td>
                </tr>                
                
                <tr>
                    <td valign="top" align="right">
                        <label for="overlap">Ocultar eventos Flash:</label>
                    </td>
                    <td>
                        <input type="checkbox" name="overlap" id="overlap" value="1" {if $advertisement->overlap == 1}checked="checked"{/if} />
                    </td>
                </tr>                
                                
                <tr>
                    <td colspan="2" style="border-bottom: 1px solid #CCC;">&nbsp;</td> 
                </tr>                
                <tr>
                    <td colspan="2" valign="top" align="left">
                        <label>Periodicidad:</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <label>
                            Indefinida<input type="radio" id="non" name="type_medida" value="NULL"
                            {if !isset($advertisement->type_medida) || $advertisement->type_medida == 'NULL'} checked="checked"{/if} onClick="permanencia(this);"/>
                        </label>
                        <span id="div_permanencia" style="display:{if $advertisement->with_script==1}none{/if};">
                            <label>Nº Clicks<input type="radio" id="clic" name="type_medida" value="CLIC"
                                {if $advertisement->type_medida == 'CLIC'}checked="checked"{/if} onClick="permanencia(this);"/></label>
                            <label>Nº Visitas<input id="view" type="radio" name="type_medida" value="VIEW"
                                {if $advertisement->type_medida == 'VIEW'}checked="checked"{/if} onClick="permanencia(this);" /></label>
                        </span>
                        
                        <label>
                            Por Fechas<input type="radio" id="fecha" name="type_medida" value="DATE"
                                {if $advertisement->type_medida == 'DATE'}checked="checked"{/if} onClick="permanencia(this);" />
                        </label>
                    </td>
                </tr>                       
                
                <tr>
                    <td valign="top" colspan="2">
                        
                        <div id="porfecha" style="width:95%;display:{if $advertisement->type_medida!='DATE'}none{/if};"> 
                            <table width="95%">
                            <tr>
                                <td valign="top" align="right" style="padding:4px;" width="40%">
                                    <label for="starttime">Inicio publicación:</label>
                                </td>
                                <td style="padding:4px;" nowrap="nowrap" width="60%">
                                    <input type="text" id="starttime" name="starttime" size="16" title="Fecha inicio publicacion"
                                        value="{if $advertisement->starttime != '0000-00-00 00:00:00'}{$advertisement->starttime}{/if}" />

                                </td>
                            </tr>
                            <tr>
                                <td valign="top" align="right" style="padding:4px;" width="40%">
                                    <label for="endtime">Fin publicación:</label>
                                </td>
                                <td style="padding:4px;" nowrap="nowrap" width="60%">
                                    <input type="text" id="endtime" name="endtime" size="16" title="Fecha fin publicacion"
                                        value="{if $advertisement->endtime != '0000-00-00 00:00:00'}{$advertisement->endtime}{/if}" />
                                  
                                </td>
                            </tr>
                            </table>
                        </div>
                        
                        <div id="porclic" style="width:95%;display:{if $advertisement->type_medida!='CLIC'}none{/if};"> 
                            <table width="95%">
                                <tr>
                                    <td valign="top" align="right" style="padding:4px;" width="40%">
                                        <label for="title">Número de clic:</label>
                                    </td>
                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                        <input type="text" id="num_clic" name="num_clic" title="Numero de clic"
                                            value="{$advertisement->num_clic}" />
										{if $smarty.request.action eq "read"}
                                            {if $advertisement->type_medida == CLIC}
                                                Actuales: {$advertisement->num_clic_count}
                                            {/if}                                        
                                            <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic"
                                                value="{$advertisement->num_clic_count}" />
                                        {/if}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div id="porview" style="width:95%;display:{if $advertisement->type_medida!='VIEW'}none{/if};"> 
                            <table width="95%">
                                <tr>
                                    <td valign="top" align="right" style="padding:4px;" width="40%">
                                        <label for="title">Número de visonados:</label>
                                    </td>
                                    <td style="padding:4px;" nowrap="nowrap" width="60%">
                                        <input type="text" id="num_view" name="num_view" title="Numero de visionados"
                                            value="{$advertisement->num_view}" />
                                        {if $smarty.request.action eq "read"}
                                            {if $advertisement->type_medida == VIEW}
                                                Actuales: {$advertisement->views}
                                            {/if}
                                        {/if}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                    <td>
                    
                </tr>
                
                <tr>
                    <td valign="top" colspan="2">                        
                        <div id="timeout_container" style="margin-top: 10px; border-top: 1px solid #CCC;width:95%;display:{if $advertisement->type_advertisement!=50}none{/if};"> 
                        
                        <label for="timeout">Temporizador:</label>
                        
                        <table width="95%">
                        <tr>
                            <td width="40%">&nbsp;</td>
                            <td style="padding:4px;" nowrap="nowrap" width="60%">
                                <input type="text" id="timeout" name="timeout" size="2" title="Segundos antes de desaparecer"
                                    value="{$advertisement->timeout|default:"4"}" style="text-align: right;" />
                                segundos. <sub>( -1 no desaparece )</sub>
                            </td>
                        </tr>                            
                        </table>
                        
                        </div>                        
                    </td>
                </tr>
                
            </table>            
        </div>
        <b class="rbottom"><b class="r4"></b><b class="r3"></b><b class="r2"></b><b class="r1"></b></b>
        
        {if $smarty.request.action eq "read"}
            <input type="hidden" id="num_clic_count" name="num_clic_count" title="Numero de clic"
                value="{$advertisement->num_clic_count}" />
        {/if}
        
    </td>
    {* end: PANEL PROPIEDADES *}
</tr>

<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" width="30%">
        <div id="div_url1" style="display:{if $advertisement->with_script==1}none{/if};">
            <label for="title">URL:</label>
        </div>
	</td>
	<td valign="top" style="height:20px;padding:4px;" nowrap="nowrap" width="70%">
        <div id="div_url2" style="display:{if $advertisement->with_script==1}none{/if};">	
            <input type="text" id="url" name="url" class="validate-url" title="Direccion web Publicidad"
                size="80" value="{$advertisement->url|default:"http://"}" />
        </div>
	</td>
</tr>

<tr>
    <td valign="top" align="right" style="height:20px;padding:4px;">
        <label for="category">Secci&oacute;n:</label>
    </td>
    <td valign="top" style="height:20px;padding:4px;">                
        <select name="category" id="category" class="required">
            <option value="0">Home</option>
            <option value="4" {if $category eq 4}selected="selected"{/if}>Opinión</option>
            <option value="9" {if $category eq 9}selected="selected"{/if}>Conecta</option>
            {section name=as loop=$allcategorys}
                <option value="{$allcategorys[as]->pk_content_category}"
                    {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if}>
                    {$allcategorys[as]->title}
                </option>						
                {section name=su loop=$subcat[as]}
                    <option value="{$subcat[as][su]->pk_content_category}"
                        {if $category eq $subcat[as][su]->pk_content_category}selected="selected"{/if}>
                        &nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}
                    </option>				  					   	     
                {/section}
            {/section}
        </select>                
    </td> 
</tr>

<tr>
	<td valign="top" align="right" style="height:20px;padding:4px;" nowrap="nowrap">		
		<label for="with_script">Publicidad con Javascript:</label>
    </td>
    <td valign="top" style="height:20px;padding:4px;">
        <input type="checkbox" id="with_script" name="with_script" value="1"
            {if $advertisement->with_script == 1}checked="checked"{/if} onClick="with_without_script(this);" />
    </td>
</tr>

<tr>
    <td valign="top" colspan="2">
        <div id="div_script" style="display:{if $advertisement->with_script!=1}none{/if}; text-align: right;">            
            <textarea name="script" id="script" class="validate-script" title="script de publicidad" style="width:100%; height:8em;">{$advertisement->script|default:'&lt;script type="text/javascript"&gt;/* Código javascript */&lt;/script&gt;'}</textarea>
            <br />
            
            <label>Recortes de código Geoip:
                <span id="geoip_select"></span>
            </label>
            
            <script type="text/javascript" src="{$params.JS_DIR}GeoipHelper.js?cacheburst=1258404035" charset="utf-8"></script>
            <script type="text/javascript">
            {literal}
            new GeoipHelper('geoip_select', 'script');
            {/literal}
            </script>
            
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button onclick="testScript(this.form);return false;">Probar Código JS</button>
		</div>
	</td>
</tr>

{* Selector de imágenes *}
{include file="advertisement_images.tpl"}
	
<tr>
	<td valign="top" style="padding:4px;padding-top: 40px;" colspan="2">
		<label for="title">Tipo Publicidad: </label>
        
        <ul id="tabs">            
            <li><a href="#publi-portada">Portada</a></li>
            <li><a href="#publi-interior">Interior</a></li>
            <li><a href="#publi-opinion">Portada Opinión</a></li>
            <li><a href="#publi-opinion-interior">Interior Opinión</a></li>
        </ul>

        <div id="publi-portada" class="panel-ads">
            {include file="advertisement_positions.tpl"}
        </div>
        
        <div id="publi-interior" class="panel-ads">
            {include file="advertisement_positions_interior.tpl"}
        </div>
        
        <div id="publi-opinion" class="panel-ads">
            {include file="advertisement_positions_opinion.tpl"}
        </div>
        
        <div id="publi-opinion-interior" class="panel-ads">
            {include file="advertisement_positions_opinion_interior.tpl"}
        </div>
	</td>
    <td>&nbsp;</td>
</tr>
</tbody>
</table>

{* if $smarty.request.action eq "read" *}
<script type="text/javascript">
/* <![CDATA[ */
{literal}
// Add exhibit method to Fabtabs to can select a tab by id
Fabtabs.addMethods({
    exhibit: function(id) {
        elm = this.element.select("a[href$="+id+"]")[0];
        this.show( elm );
        this.menu.without(elm).each(this.hide.bind(this));
        
        if(['publi-opinion', 'publi-opinion-interior'].indexOf(id)==-1) {
            this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', {display: 'none'});
            this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', {display: ''});
        } else {
            this.changePropertyTabs('a[href$=publi-portada],a[href$=publi-interior]', {display: 'none'});
            this.changePropertyTabs('a[href$=publi-opinion],a[href$=publi-opinion-interior]', {display: ''});
        }
    },
    
    changePropertyTabs: function(tabSelector, style) {
        this.element.select(tabSelector).each(function(item) {
            item.setStyle(style);
        });
    },        
    
    activate :  function(ev) {
		var elm = Event.findElement(ev, "a");
        var id = elm.getAttribute('href').gsub(/#(.*?)$/, '#{1}');
        
		Event.stop(ev);        
        if( $F('category') == '4' && (['publi-opinion', 'publi-opinion-interior'].indexOf(id)!=-1)) {
            this.show(elm);
            this.menu.without(elm).each(this.hide.bind(this));
        }
        
        if( $F('category') != '4' && (['publi-opinion', 'publi-opinion-interior'].indexOf(id)==-1)) {
            this.show(elm);
            this.menu.without(elm).each(this.hide.bind(this));
        }
	}
});

$('category').observe('change', function() {
    if(this.value == '4') {
        $fabtabs.exhibit('publi-opinion');
    } else {
        $fabtabs.exhibit('publi-portada');        
    }
});
{/literal}
document.observe('dom:loaded', function() {ldelim}
{if $category ne '4'}
    {if $advertisement->type_advertisement lt 100}
        $fabtabs.exhibit('publi-portada');
    {else}
        $fabtabs.exhibit('publi-interior');    
    {/if}
{else}
    {if $advertisement->type_advertisement ne 101 && $advertisement->type_advertisement ne 5}
        $fabtabs.exhibit('publi-opinion');
    {else}
        $fabtabs.exhibit('publi-opinion-interior');    
    {/if}
{/if}
{rdelim});

{literal}

{/literal}
/* ]]> */
</script>
{* /if *}

{* FIXME: replace by DatePicker }
{dhtml_calendar inputField="starttime" button="triggerstart"  ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR"}
{dhtml_calendar inputField="endtime" button="triggerend"  ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR"}
*}
<script type="text/javascript" language="javascript">
{literal}


if($('starttime')) {
    new Control.DatePicker($('starttime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

    new Control.DatePicker($('endtime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });
}
{/literal}
</script>

<input type="hidden" name="filter[type_advertisement]" value="{$smarty.request.filter.type_advertisement}" />
<input type="hidden" name="filter[available]" value="{$smarty.request.filter.available}" />
<input type="hidden" name="filter[type]" value="{$smarty.request.filter.type}" />
{/if}


{* ******************************************************************************* *}
{* Vista para a proba do script de publicidade, esta tpl abrirase nunha nova ventá *}
{if isset($smarty.request.action) && $smarty.request.action eq "test_script"}
    <h1>Prueba script publicidad</h1>
    <div style="text-align:center; border: 2px dashed #CCC;">
        {$script}
    </div>
    
    <div align="right" style="margin: 10px 10px;">
        <a href="#" style="color: #666; font-size: large;"
            onclick="window.close();" title="Cerrar ventana">[Cerrar]</a>
    </div>
{/if}


{* ******************************************************************************* *}
{include file="footer.tpl"}