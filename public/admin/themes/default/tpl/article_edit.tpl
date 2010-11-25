
<div id="warnings-validation">
{if $article->isClone()}
    {assign var="original" value=$article->getOriginal()}
    
    Este artículo fue <strong>clonado</strong>. <br /> Para editar contenidos propios del artículo ir al&nbsp; <a href="article.php?action=read&id={$original->id}">artículo original</a>.
{/if}
</div>

{* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
<ul id="tabs">
    <li>
        <a href="#edicion-contenido">Edici&oacute;n noticia</a>
    </li>    
    <li>
        <a href="#edicion-extra" onClick="javascript:get_tags($('title').value);">Par&aacute;metros de noticia</a>
    </li>
    {if !$article->isClone()}
    <li>
        <a href="#comments">Comentarios</a>
    </li>
    {/if}
    <li>
        <a href="#contenidos-relacionados">Contenidos relacionados</a>
    </li>
    <li>
        <a href="#elementos-relacionados" onClick="mover();">Organizar relacionados</a>
    </li>
    {if isset($clones)}
    <li>
        <a href="#clones">Clones</a>
    </li>
    {/if}
</ul>


<div class="panel" id="edicion-contenido" style="width:98%">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="96%">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="10%">
        <label for="title">T&iacute;tulo:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
        <input type="text" id="title" name="title" title="Título de la noticia" tabindex="1"
            value="{$article->title|clearslash|escape:"html"}" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title'));  search_related('{$article->pk_article}',$('metadata').value);"  onkeyup="countWords(this,document.getElementById('counter_title'))"  onkeyup="countWords(this,document.getElementById('counter_title'))"/>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="10%">
        <label for="title">T&iacute;tulo Interior:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" valign="top" width="50%">
        <input type="text" id="title_int" name="title_int" title="Título de la noticia interior"
            value="{$article->title_int|clearslash|escape:"html"}" class="required" size="80" maxlength="105" onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);" onkeyup="countWords(this,document.getElementById('counter_title_int'))" tabindex="1"/>
        
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
                <td valign="top"  align="right" nowrap="nowrap">
                <label for="category">Secci&oacute;n:</label>
                </td>
                <td nowrap="nowrap" style="text-align:left;">
                   <select name="category" id="category" class="validate-section" onChange="get_tags($('title').value);" tabindex="6">
                        <option value="20" {if $category eq $allcategorys[as]->pk_content_category}selected="selected"{/if} name="{$allcategorys[as]->title}" >UNKNOWN</option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $article->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}">{$allcategorys[as]->title}</option>
                            {section name=su loop=$subcat[as]}
                                {if $subcat[as][su]->internal_category eq 1}
                                    <option value="{$subcat[as][su]->pk_content_category}" {if $article->category  eq $subcat[as][su]->pk_content_category} selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                               
                               {/if}
                            {/section}
                        {/section}
                    </select>
                </td>
            </tr>
            {if $smarty.session.desde != 'list_hemeroteca'}
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="with_comment"> Comentarios: </label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                    <select name="with_comment" id="with_comment" class="required" tabindex="7">
                        <option value="0" {if $article->with_comment eq 0}selected="selected"{/if}>No</option>
                        <option value="1" {if $article->with_comment eq 1}selected="selected"{/if}>Si</option>
                    </select>
                </td>
            </tr>           
            <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="frontpage"> En Portada Sección: </label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="frontpage" id="frontpage" class="required" tabindex="8">
                            <option value="0" {if $article->frontpage eq 0}selected="selected"{/if}>No</option>
                            <option value="1" {if $article->frontpage eq 1}selected="selected"{/if}>Si</option>
                       </select>
                    </td>
            </tr>
            <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="frontpage"> En Home: </label>
                    </td>
                    <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                        <select name="in_home" id="in_home" class="required" tabindex="8">
                            <option value="0" {if $article->in_home eq 0}selected="selected"{/if}>No</option>
                            <option value="1" {if $article->in_home eq 1}selected="selected"{/if}>Si</option>
                       </select>
                    </td>
            </tr>
            <tr>
                    <td valign="top"  align="right" nowrap="nowrap">
                        <label for="available"> Disponible: </label>
                    </td>
                        <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                        <select name="available" id="available" class="required" tabindex="9">
                            <option value="1" {if $article->available eq 1} selected {/if}>Si</option>
                            <option value="0" {if $article->available eq 0} selected {/if}>No</option>
                       </select>
                        <img class="inhome" src="{$params.IMAGE_DIR}available.png" border="0" alt="Disponible" align="top" />
                </td>
            </tr>
            {* Hai que ter en conta o placeholder, non a posición *}
            {* if ($article->position) eq 1 && ($_from neq 'home') *}            
            {* if ($article->placeholder eq 'placeholder_0_0') && ($_from neq 'home')}
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="columns"> Columnas: </label>
                </td>
                <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                    <select name="columns" id="columns" class="required" tabindex="10">
                    <option value="1" {if $article->columns eq 1} selected {/if}>1</option>
                    <option value="2" {if $article->columns eq 2} selected {/if}>2</option>
                   </select>
                </td>
            </tr>
            {/if *}
            
            {* Hai que ter en conta o placeholder, non a posición *}
            {* if ($article->home_pos) eq 1 && ($_from eq 'home') *}
            {* if ($article->home_placeholder eq 'placeholder_0_0') && ($_from eq 'home')}
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="home_columns"> Home Columnas: </label>
                </td>
                <td  style="text-align:left;vertical-align:top" nowrap="nowrap">
                    <select name="home_columns" id="home_columns" class="required" tabindex="11">
                    <option value="1" {if $article->home_columns eq 1}selected="selected"{/if}>1</option>
                    <option value="2" {if $article->home_columns eq 2}selected="selected"{/if}>2</option>
                   </select>
                </td>
            </tr>
            {/if *}
            
            {else} {* else if not list_hemeroteca *}
            
            <tr>
                <td valign="top"  align="right" nowrap="nowrap">
                    <label for="with_comment"> Archivado: </label>
                </td>
                <td nowrap="nowrap" style="text-align:left;vertical-align:top">
                    <select name="content_status" id="content_status" class="required">
                        <option value="0" {if $article->content_status eq 0}selected="selected"{/if}>Si</option>
                        <option value="1" {if $article->content_status eq 1}selected="selected"{/if}>No</option>
                    </select>
                    {* <input type="hidden" id="content_status" name="content_status"  value="{$article->content_status}" /> *}
                    
                    <input type="hidden" id="columns" name="columns"  value="{$article->columns}" />
                    <input type="hidden" id="home_columns" name="home_columns"  value="{$article->home_columns}" />
                    <input type="hidden" id="with_comment" name="with_comment"  value="{$article->with_comment}" />
                    <input type="hidden" id="available" name="available"  value="{$article->available}" />
                    <input type="hidden" id="in_home" name="in_home"  value="{$article->in_home}" />                    
                </td>
            </tr>
                
     
            {/if} 
            <tr>
                <td valign="top" align="right">
                    <label for="counter_title">T&iacute;tulo:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_title" name="counter_title" title="counter_title" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
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
                <td nowrap="nowrap" style="text-align:left;vertical-align:top" >
                    <input type="text" id="counter_subtitle" name="counter_subtitle" title="counter_subtitle" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('subtitle'),this)"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_summary">Entradilla:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_summary" name="counter_summary" title="counter_summary" tabindex="-1"
                        value="0" class="required" size="5" onkeyup="countWords(document.getElementById('summary'),this)"/>
                </td>
            </tr>
            <tr>
                <td valign="top" align="right">
                    <label for="counter_body">Cuerpo:</label>
                </td>
                <td nowrap="nowrap"  style="text-align:left;vertical-align:top">
                    <input type="text" id="counter_body" name="counter_body" title="counter_body" tabindex="-1"
                        value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
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
        <input type="text" id="metadata" name="metadata" size="70" title="Metadatos" value="{$article->metadata}" onChange="search_related('{$article->pk_article}',$('metadata').value);" tabindex="1" />
        <sub>Separadas por comas</sub>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="subtitle">Antet&iacute;tulo:</label>
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap">
        <input type="text" id="subtitle" name="subtitle" title="antetítulo" tabindex="2"
            value="{$article->subtitle|upper|clearslash|escape:"html"}" class="required" size="100" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" />
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="agency">Agencia:</label>
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap" >
        <input type="text" id="agency" name="agency" title="Agencia" tabindex="3"
            value="{$article->agency|clearslash|escape:"html"}" class="required" size="100" 
   {literal}onblur="setTimeout(function(){tinyMCE.get('summary').focus();}, 200);"{/literal} />
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;">
        <label for="summary">Entradilla:</label><br />
        {if !$article->isClone()}
        <a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
                <img src="{$params.IMAGE_DIR}/users_edit.png" alt="" border="0" /></a>
        {/if}
    </td>
    <td style="padding:4px;" valign="top" nowrap="nowrap">
        <textarea name="summary" id="summary" tabindex="4"
            title="Resumen de la noticia" style="width:100%; height:8em;"  onChange="countWords(this,document.getElementById('counter_summary'))" onkeyup="countWords(this,document.getElementById('counter_summary'))">{$article->summary|clearslash|escape:"html"}</textarea>
    </td>    
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" >
        <label for="body">Cuerpo:</label>
    </td>
    <td style="padding-bottom: 5px; padding-top: 10px;" valign="top" nowrap="nowrap" colspan='2'>
        <textarea name="body" id="body" tabindex="5"
            title="Cuerpo de la noticia" style="width:100%; height:20em;" onChange="counttiny(document.getElementById('counter_body'));"> {$article->body|clearslash}</textarea>
    </td>
</tr>
    
<tr><td></td>
    <td valign="top" align="center" colspan=2 >
        <div id="article_images" style="display:inline;">
            {include file="article_images_edit.tpl"}
        </div>
        
    </td>    
</tr>    
<tr>
    <td> </td>
    <td valign="top" align="left" colspan="2">&nbsp;</td>
</tr>
</tbody>
</table>

{literal}
<script type="text/javascript">
    countWords(document.getElementById('title'), document.getElementById('counter_title'));
    countWords(document.getElementById('subtitle'), document.getElementById('counter_subtitle'));
    countWords(document.getElementById('summary'), document.getElementById('counter_summary'));
    countWords(document.getElementById('body'), document.getElementById('counter_body'));
</script>
{/literal}
</div>


<div class="panel" id="edicion-extra" style="width:95%">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
<tbody>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="starttime">Fecha inicio publicaci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <div style="width:170px;">
            <input type="text" id="starttime" name="starttime" size="18" title="Fecha inicio publicaci&oacute;n"
                value="{$article->starttime}" tabindex="-1"/></div>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="endtime">Fecha fin publicaci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <div style="width:170px;">
            <input type="text" id="endtime" name="endtime" size="18" title="Fecha fin publicaci&oacute;n"
                value="{$article->endtime}" tabindex="-1"/></div>
        <sub>Hora del servidor: {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</sub>
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="permalink">Permalink:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <input type="text" id="p" readonly="readonly" size="100"
            title="permalink la noticia" value="{$article->permalink}" tabindex="-1" />
        {* <a href="{$article->permalink}" target="_blank" onclick="UserVoice.PopIn.showPublic('{$article->permalink}');return false;">
            [Probar permalink]</a> *}
    </td>
</tr>
<tr>
    <td valign="top" align="right" style="padding:4px;" width="30%">
        <label for="description">Descripci&oacute;n:</label>
    </td>
    <td style="padding:4px;" nowrap="nowrap" width="70%">
        <textarea name="description" id="description"
            title="Descripción interna de la noticia" style="width:100%; height:8em;" tabindex="-1">{$article->description|clearslash}</textarea>
    </td>
</tr>

</tbody>
</table>
</div>


<div class="panel" id="comments" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="4" class="fuente_cuerpo" width="99%">
    <tbody>
    <tr>                
        <th class="title" width='50%'>Comentario</th>
        <th class="title"  width='20%'>Autor</th>                    
        <th align="right">Publicar</th>        
        <th align="right">Eliminar</th>
    </tr>                
        {section name=c loop=$comments}
        <tr>
            <td>  <a style="cursor:pointer;font-size:14px;" onclick="new Effect.toggle($('{$comments[c]->pk_comment}'),'blind')"> {$comments[c]->body|truncate:30} </a> </td>
            <td> {$comments[c]->author} ({$comments[c]->ip})  <br /> {$comments[c]->email} </td>
            <td align="right"> {* {if $comments[c]->content_status == 1}
                    <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=0&amp;category={$category}" title="Publicado">
                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                {else}
                    <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}" title="Pendiente">
                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                {/if} *}
             </td>
             <td align="right">     
                 <a href="#" onClick="javascript:confirmar(this, '{$comments[c]->pk_comment}');" title="Eliminar">
                <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
           </td>
        </tr>
        <tr><td>
          <div id="{$comments[c]->pk_comment}" class="{$comments[c]->pk_comment}" style="display: none;">    
           <b>Comentario:</b> (IP: {$comments[c]->ip} - Publicado: {$comments[c]->changed})<br /> {$comments[c]->body}
          </div>
         </td></tr>
    {/section}    
    <td colspan="5">
    {*     <a href="#" onclick="new Effect.toggle($('comment'),'blind')"> <img src='images/iconos/examinar.gif' border='0'> Añadir comentario </a>
             <br />    
        {include file="new_comment.tpl"} 
        *}
     </td>
    </tbody>
    </table>
</div>


<div class="panel" id="opiniones-relacionadas" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
    <tbody><tr>
    <td colspan="2">
     opiniones
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
        <ul id="thelist2" style="padding: 4px; background: #EEEEEE">
              {assign var=cont value=1}
            {section name=n loop=$losrel}
                <li id="{$losrel[n]->id|clearslash}">
                    <table  width="99%">
                        <tr>
                            <td>{$losrel[n]->title|clearslash|escape:'html'}</td>
                            <td width='120'> {* if $losrel[n]->content_type eq 1}Noticia{elseif  $losrel[n]->content_type eq 7} Fotogaleria  
                                {elseif  $losrel[n]->content_type eq 9} Video {elseif  $losrel[n]->content_type eq 4} Opinion 
                                {elseif $losrel[n]->content_type eq 3} Archivo {/if *}
                                {assign var="ct" value=$losrel[n]->content_type}
                                {$content_types.$ct}
                            </td> 
                            <td width="120"> {$losrel[n]->category_name|clearslash} </td> 
                            <td width="120">
                                <a  href="#" onClick="javascript:del_relation('{$losrel[n]->id|clearslash}','thelist2');" title="Quitar relacion">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" /> </a>
                            </td>
                        </tr>
                    </table>
                </li>
                {assign var=cont value=$cont+1}
            {/section}
        </ul>
    </div>
    <br />
    Listado contenidos relacionados en Interior:  <br />
    <div style="position:relative;" id="scroll-container2int">
        <ul id="thelist2int" style="padding: 4px; background: #EEEEEE">
            {assign var=cont value=1}
            {section name=n loop=$intrel}
            <li id="{$intrel[n]->id|clearslash}"">
                <table  width='99%'>
                    <tr>
                        <td>{$intrel[n]->title|clearslash|escape:'html'}  </td> 
                        <td width='120'> {* if $intrel[n]->content_type eq 1}Noticia{elseif  $intrel[n]->content_type eq 7} Galeria  
                            {elseif  $intrel[n]->content_type eq 9} Video {elseif  $intrel[n]->content_type eq 4} Opinion 
                            {elseif $intrel[n]->content_type eq 3} Fichero {/if *}
                            {assign var="ct" value=$intrel[n]->content_type}
                            {$content_types.$ct}
                        </td> 
                        <td width='120'> {$intrel[n]->category_name|clearslash} </td> 
                        <td width='120'>
                            <a  href="#" onClick="javascript:del_relation('{$intrel[n]->id|clearslash}','thelist2int');" title="Quitar relacion">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" /> </a> 
                        </td>
                    </tr>
                </table>  
             </li>
              {assign var=cont value=$cont+1}
            {/section}
        </ul>
    </div>
    <br /><br />
    
    <div class="p">
        <input type="hidden" id="ordenPortada" name="ordenArti" value="" size="140"></input>
        <input type="hidden" id="ordenInterior" name="ordenArtiInt" value="" size="140"></input>
    </div>
</div>

{if isset($clones)}
<div class="panel" id="clones" style="width:95%">
    {include file="article_clones.tpl"}
</div>
{/if}

{if $article->isClone()}
{* Disable fields via javascript if $article->isClone() *}
{literal}
<script type="text/javascript">
(function() {
    var elements = ['title', 'description', 'subtitle', 'metadata', 'agency'];
    elements.each(function(item){
        $(item).disabled = true;
        $(item).setAttribute('disabled', 'disabled');
    });
}());
</script>
{/literal}
{/if}
