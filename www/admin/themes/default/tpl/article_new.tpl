
<div id="warnings-validation"></div>
{* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
<ul id="tabs">
	<li>
		<a href="#edicion-contenido">Edici&oacute;n noticia</a>
	</li>	
	<li>
		<a href="#edicion-extra">Par&aacute;metros de noticia</a>
	</li>	
	<li>
		<a href="#comments">Comentarios</a>
	</li>
	<li>
		<a href="#contenidos-relacionados">Contenidos relacionados</a>
	</li>
	<li>
		<a href="#elementos-relacionados" onClick="mover();">Organizar relacionados</a>
	</li> 
</ul>

{* Pestaña de edición ******************************************************** *}
<div class="panel" id="edicion-contenido" style="width:98%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="96%">
    <tbody>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="10%">
            <label for="title">T&iacute;tulo Portada:</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
            <input type="text" id="title" name="title" title="Título de la noticia en portada"
                value="" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title'));" onkeyup="countWords(this,document.getElementById('counter_title'))" tabindex="1"/>
        </td>
    </tr>
    <tr>
         <td valign="top" align="right" style="padding:4px;" width="10%">
            <label for="title">T&iacute;tulo Interior:</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
            <input type="text" id="title_int" name="title_int" title="Título de la noticia interior"
                value="" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);" onkeyup="countWords(this,document.getElementById('counter_title_int'))" tabindex="1"/>
            
            
            {* input#title.onblur() cando title perda o foco copiar en título interior *}
            {literal}
            <script type="text/javascript">
            /* <![CDATA[ */
            $('title').observe('blur', function(evt) {
                var tituloInt = $('title_int').value.strip();
                if( tituloInt.length == 0 ) {
                    $('title_int').value = $F('title');
                    get_tags($('title_int').value);
                }                
            });
            /* ]]> */
            </script>
            {/literal}
            
        </td>
        <td valign="top" align="right" style="padding:4px;" rowspan="5" width="40%">
            <div class="utilities-conf">
            <table style="width:99%;">
                <tr>
                    <td valign="top" align="right" nowrap="nowrap">
                    <label for="category">Secci&oacute;n:</label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="category" id="category" class="validate-section" onChange="get_tags($('title').value);"  tabindex="6">
                               <option value="20" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >UNKNOWN</option>
                            {section name=as loop=$allcategorys}                                                 
                                <option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                {section name=su loop=$subcat[as]}
                                    <option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                {/section}
                            {/section}
                        </select>
                    </td>
                </tr>
                 <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="with_comment"> Disponible: </label>
                    </td>
                    <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                        <select name="content_status" id="content_status" class="required" tabindex="7">
                            <option value="0" selected="selected">No</option>
                            <option value="1" >Si</option>
                       </select>
                        <span style="font-size:9px;">(publicar directamente)</span>
                    </td>
                </tr>
                <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="with_comment"> Comentarios: </label>
                    </td>
                    <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                        <select name="with_comment" id="with_comment" class="required" tabindex="7">
                            <option value="0" >No</option>
                            <option value="1" selected="selected">Si</option>
                       </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="in_home"> En portada: </label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="in_home" id="in_home" class="required" tabindex="8">
                            <option value="0" {if $article->in_home eq 0}selected="selected"{/if}>No</option>
                            <option value="1" {if $article->in_home eq 1}selected="selected"{/if}>Si</option>
                            {* <option value="2" {if $article->in_home eq 2}selected="selected"{/if}>Si</option> *} 
                       </select>
                       {* <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="En home" align="top" /> *}
                    </td>
               </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="counter_title">T&iacute;tulo:</label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
                        <input type="text" id="counter_title" name="counter_title" title="counter_title"
                            value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)" tabindex="-1"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="counter_title">T&iacute;tulo Interior:</label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
                        <input type="text" id="counter_title_int" name="counter_title_int" title="counter_title_int"
                            value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title_int'),this)" tabindex="-1"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="counter_subtitle">Antet&iacute;tulo:</label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
                        <input type="text" id="counter_subtitle" name="counter_subtitle" title="counter_subtitle"
                            value="0" class="required" size="5" onkeyup="countWords(document.getElementById('subtitle'),this)" tabindex="-1"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="counter_summary">Entradilla:</label>
                    </td>
                    <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                        <input type="text" id="counter_summary" name="counter_summary" title="counter_summary"
                            value="0" class="required" size="5" onChange="countWords(document.getElementById('summary'),this)" onkeyup="countWords(document.getElementById('summary'),this)" tabindex="-1"/>
                    </td>
                </tr>
                <tr>
                    <td valign="top" align="right">
                        <label for="counter_body">Cuerpo:</label>
                    </td>
                    <td nowrap="nowrap"  style="text-align:left;vertical-align:top" >
                        <input type="text" id="counter_body" name="counter_body" title="counter_body"
                            value="0" class="required" size="5" onChange="counttiny(document.getElementById('counter_body'));" onkeyup="counttiny(document.getElementById('counter_body'));" tabindex="-1"/>
                    </td>
                </tr>
            </table>
            </div>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="30%">
            <label for="metadata">Palabras clave: </label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" width="70%">
            <input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="" tabindex="1"/>
            <sub>Separadas por comas</sub>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="subtitle">Antet&iacute;tulo:</label>
        </td>
        <td style="padding:4px;" valign="top" nowrap="nowrap" >
            <input type="text" id="subtitle" name="subtitle" title="antetítulo"
                value="" class="required" size="100" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" tabindex="2"/>
        </td>
    </tr>
    
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="agency">Agencia:</label>
        </td>
        <td style="padding:4px;" valign="top" nowrap="nowrap" >
            <input type="text" id="agency" name="agency" title="Agencia"
                value="Agencias" class="required" size="100" tabindex="3"
{literal}onblur="setTimeout(function(){tinyMCE.get('summary').focus();}, 200);"{/literal} />
        </td>
    </tr>
    
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="summary">Entradilla:</label><br />
            <a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
                <img src="{$params.IMAGE_DIR}/users_edit.png" alt="" border="0" /></a>
        </td>
        <td style="padding:4px;" valign="top" nowrap="nowrap">
            <textarea name="summary" id="summary" title="Resumen de la noticia" style="width:100%; height:8em;"
                  onChange="countWords(this,document.getElementById('counter_summary'))"
                  onkeyup="countWords(this,document.getElementById('counter_summary'))" tabindex="4"></textarea>                        
        </td>	
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="body">Cuerpo:</label>
        </td>
        <td style="padding-bottom: 5px; padding-top: 10px;" valign="top" nowrap="nowrap" colspan="2">
            <textarea name="body" id="body" title="Cuerpo de la noticia" style="width:100%; height:20em;" onChange="counttiny(document.getElementById('counter_body'));" tabindex="5"></textarea>
        </td>
    </tr>	
    <tr>
        <td></td>
        <td valign="top" align="center" colspan="2" >
            <div id="article_images" style="display:inline;">
                {include  file="article_images.tpl"}
            </div>
        </td>	
    </tr>
    
    <tr>
        <td></td>
        <td valign="top" align="left" colspan="2">		
            
        </td>
    </tr>
    </tbody>
    </table>
</div>

{* Pestaña de parámetros de noticia ****************************************** *}
<div class="panel" id="edicion-extra" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
    <tbody>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="30%">
            <label for="starttime">Fecha inicio publicaci&oacute;n:</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" width="70%">
            <div style="width:170px;">
                <input type="text" id="starttime" name="starttime" size="18"
                    title="Fecha inicio publicaci&oacute;n" value="" tabindex="-1" /></div>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="30%">
            <label for="endtime">Fecha fin publicaci&oacute;n:</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" width="70%">
            <div style="width:170px;">
                <input type="text" id="endtime" name="endtime" size="18"
                    title="Fecha fin publicaci&oacute;n" value="" tabindex="-1" /></div>
                
            <sub>Hora del servidor: {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</sub>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" width="30%">
            <label for="description">Descripci&oacute;n:</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" width="70%">
            <textarea name="description" id="description"
                title="Descripción interna de la noticia" style="width:100%; height:8em;" tabindex="-1"></textarea>
        </td>
    </tr>
 {*   <tr>
        <td valign="top" align="right" style="padding:4px;" width="30%">
            <label for="metadata">Palabras clave: </label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" width="70%">
            <input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="" tabindex="-1"/>
            <sub>Separadas por comas</sub>
        </td>
    </tr>
    *}
    </tbody>
    </table>
</div>

<div class="panel" id="comments" style="width:95%">
	<table border="0" cellpadding="0" cellspacing="4" class="fuente_cuerpo" width="99%">
	<tbody>
	<tr>				
		<th class="title" width="50%">T&iacute;tulo</th>
		<th class="title" width="20%">Autor</th>				
		<th align="right">Publicar</th>		
		<th align="right">Eliminar</th>
	</tr>		
	<tr>
        <td colspan="5">
            {* Si o div con $('comment') está dentro de new_comment.tpl, ¿por qué non incluilo en new_comment.tpl? *}
            <a href="#" onclick="new Effect.toggle($('comment'),'blind')"> <img src='images/iconos/examinar.gif' border='0'> Añadir comentario </a>
            <br />	
            {include file="new_comment.tpl"}
        </td>
	</tr>
	</tbody>
	</table>
</div>

<div class="panel" id="contenidos-relacionados" style="width:95%"> 
	{include file="article_relacionados.tpl"} 		
</div>


<div class="panel" id="elementos-relacionados" style="width:95%">
    <br />
    Listado contenidos relacionados en Portada:  <br />
    <div style="position:relative;" id="scroll-container2">
        <ul id="thelist2" style="padding: 4px; background: #EEEEEE"></ul>
    </div>
    <br>
    Listado contenidos relacionados en Interior:  <br />
    <div style="position:relative;" id="scroll-container2int">
        <ul id="thelist2int" style="padding: 4px; background: #EEEEEE"></ul>
    </div>
    <br /><br />
    
    <div class="p">
        <input type="hidden" id="ordenPortada" name="ordenArti" value="" size="140"></input>
        <input type="hidden" id="ordenInterior" name="ordenArtiInt" value="" size="140"></input>
    </div>
</div>
<input type="hidden" name="available"></input>
