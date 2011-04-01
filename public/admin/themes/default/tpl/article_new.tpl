<div id="warnings-validation">
{if is_object($article) && $article->isClone()}
	{assign var="original" value=$article->getOriginal()}
    Este artículo fue <strong>clonado</strong>. <br /> Para editar contenidos propios del artículo ir al&nbsp; <a href="article.php?action=read&id={$original->id}">artículo original</a>.
{/if}
</div>



<div id="warnings-validation"></div>
{* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
<ul id="tabs">
	<li>
		<a href="#edicion-contenido">{t}Article content{/t}</a>
	</li>
	<li>
		<a href="#edicion-extra">{t}Article parameters{/t}</a>
	</li>
	{if is_object($article) && !$article->isClone()}
    <li>
		<a href="#comments">{t}Comments{/t}</a>
    </li>
    {/if}
	<li>
        <a href="#contenidos-relacionados">{t}Related contents{/t}</a>
	</li>
	<li>
        <a href="#elementos-relacionados" onClick="mover();">{t}Sort related contents{/t}</a>
	</li>
	{if is_object($article) && isset($clones)}
		<li>
			<a href="#clones">Clones</a>
		</li>
    {/if}
</ul>

{* Pestaña de edición ******************************************************** *}
<div class="panel" id="edicion-contenido" style="background:#fff;">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
    <tbody>
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="title">{t}Frontpage title:{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" valign="top" >
            <input type="text" id="title" name="title" title="Título de la noticia en portada"
                value="{$article->title|clearslash|escape:"html"}" class="required" style="width:95%" maxlength="256" onChange="countWords(this,document.getElementById('counter_title'));"
				   onkeyup="countWords(this,document.getElementById('counter_title'))"
				   {if is_object($article)}
				   onChange="search_related('{$article->pk_article}',$('metadata').value);"
				   {/if}
				   tabindex="1"/>
        </td>
		<td valign="top" align="right" style="padding:4px; width:30%" rowspan="6">
            <div class="utilities-conf">
				<table style="width:99%;">
					<tr>
						<td valign="top" align="right" nowrap="nowrap">
						<label for="category">{t}Section:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top">
							<select name="category" id="category" class="validate-section" onChange="get_tags($('title').value);"  tabindex="6">
								   <option value="20" {if $category eq $allcategorys[as]->pk_content_category || $article->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{t}Unknown{/t}</option>
								{section name=as loop=$allcategorys}
									<option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $article->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
									{section name=su loop=$subcat[as]}
										{if $subcat[as][su]->internal_category eq 1}
											<option value="{$subcat[as][su]->pk_content_category}" {if $category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
										{/if}
									{/section}
								{/section}
							</select>
						</td>
					</tr>
					{if $smarty.session.desde != 'list_hemeroteca'}
					 <tr>
						<td valign="top"  align="right" nowrap="nowrap">
							<label for="with_comment">{t}Coments{/t}</label>
						</td>
						<td  style="text-align:left;vertical-align:top" nowrap="nowrap">
							<select name="with_comment" id="with_comment" class="required" tabindex="7">
								<option value="0" >{t}Disabled{/t}</option>
								<option value="1" selected="selected">{t}Enabled{/t}</option>
						   </select>
						</td>
					</tr>
					 <tr>
						<td valign="top"  align="right" nowrap="nowrap">
							<label for="with_comment">{t}Available:{/t}</label>
						</td>

						<td  style="text-align:left;vertical-align:top" nowrap="nowrap">
							<select name="content_status" id="content_status" class="required" tabindex="7">
								<option value="0" {if $article->available eq 0}selected{/if}>{t}No{/t}</option>
								<option value="1" {if $article->available eq 1}selected{/if}>{t}Yes{/t}</option>
						   </select>
							<span style="font-size:9px;"{t}(publish directly){/t}</span>
						</td>
					</tr>
					<tr>
						<td valign="top"  align="right" nowrap="nowrap">
							<label for="frontpage">{t}Put in section frontpage:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top">
							<select name="frontpage" id="frontpage" class="required" tabindex="8">
								<option value="0" {if $article->frontpage eq 0}selected="selected"{/if}>{t}No{/t}</option>
								<option value="1" {if $article->frontpage eq 1}selected="selected"{/if}>{t}Yes{/t}</option>
						   </select>
						</td>
					</tr>
					<tr>
						<td valign="top"  align="right" nowrap="nowrap">
							<label for="frontpage">{t}Put in frontpage:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top">
							<select name="in_home" id="in_home" class="required" tabindex="8">
								<option value="0" {if $article->in_home eq 0}selected="selected"{/if}>{t}No{/t}</option>
								{*<option value="1" {if $article->in_home eq 1}selected="selected"{/if}>{t}Yes{/t}</option>*}
								<option value="2" {if ($article->in_home eq 2) or !(isset($article->in_home))}selected="selected"{/if}>{t}Just suggest{/t}</option>
						   </select>
						</td>
					</tr>
					{else} {* else if not list_hemeroteca *}
					<tr>
						<td valign="top"  align="right" nowrap="nowrap">
							<label for="with_comment">{t}Archived:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top">
							<select name="content_status" id="content_status" class="required">
								<option value="0" {if $article->content_status eq 0}selected="selected"{/if}>{t}Yes{/t}</option>
								<option value="1" {if $article->content_status eq 1}selected="selected"{/if}>{t}No{/t}</option>
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
							<label for="counter_title">{t}Frontpage title:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
							<input type="text" id="counter_title" name="counter_title" title="counter_title"
								value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)" tabindex="-1"/>
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label for="counter_title">{t}Inner title{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
							<input type="text" id="counter_title_int" name="counter_title_int" title="counter_title_int"
								value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title_int'),this)" tabindex="-1"/>
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label for="counter_subtitle">{t}Pretitle:{/t}</label>
						</td>
						<td nowrap="nowrap" style="text-align:left;vertical-align:top;" >
							<input type="text" id="counter_subtitle" name="counter_subtitle" title="counter_subtitle"
								value="0" class="required" size="5" onkeyup="countWords(document.getElementById('subtitle'),this)" tabindex="-1"/>
						</td>
					</tr>
					<tr colspan=2>
						<td valign="top" align="right">
							<label for="counter_summary">{t}Summary:{/t}</label>
						</td>
						<td nowrap="nowrap"  style="text-align:left;vertical-align:top">
							<input type="text" id="counter_summary" name="counter_summary" title="counter_summary"
								value="0" class="required" size="5"
								   onChange="countWords(document.getElementById('summary'),this)"
								   onkeyup="countWords(document.getElementById('summary'),this)" tabindex="-1"/>
						</td>
					</tr>
					<tr>
						<td valign="top" align="right">
							<label for="counter_body">{t}Body:{/t}</label>
						</td>
						<td nowrap="nowrap"  style="text-align:left;vertical-align:top" >
							<input type="text" id="counter_body" name="counter_body" title="counter_body"
								value="0" class="required" size="5" onChange="counttiny(document.getElementById('counter_body'));" onkeyup="counttiny(document.getElementById('counter_body'));" tabindex="-1"/>
						</td>
					</tr>
				</table>
				<script type="text/javascript">
					document.observe("dom:loaded", function() {
						countWords(document.getElementById('title'), document.getElementById('counter_title'));
						countWords(document.getElementById('subtitle'), document.getElementById('counter_subtitle'));
						countWords(document.getElementById('summary'), document.getElementById('counter_summary'));
						countWords(document.getElementById('body'), document.getElementById('counter_body'));
					});
				</script>
            </div>
        </td>
    </tr>
    <tr>
         <td valign="top" align="right" style="padding:4px;" >
            <label for="title">{t}Inner title:{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" valign="top" >
            <input 	type="text" id="title_int" name="title_int" title="{t}Inner title:{/t}"
					value="{$article->title_int|clearslash|escape:"html"}" class="required" style="width:95%"
					maxlength="256"
					onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);"
					onkeyup="countWords(this,document.getElementById('counter_title_int'))"
					tabindex="1"/>

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

        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" >
            <label for="metadata">{t}Keywords{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" >
            <input type="text" id="metadata" name="metadata"
				   {if is_object($article)}
				   value="{$article->metadata}"
				   onChange="search_related('{$article->pk_article}',$('metadata').value);"
				   {/if}
				   style="width:70%" title="Metadatos" tabindex="1"/>

            <sub>{t}Separated by commas{/t}</sub>
        </td>
    </tr>

    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="subtitle">{t}Pretitle{/t}</label>
        </td>
        <td style="padding:4px;" valign="top" nowrap="nowrap" >
            <input type="text" id="subtitle" name="subtitle" title="antetítulo" style="width:95%"
                value="{$article->subtitle|upper|clearslash|escape:"html"}" class="required" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" tabindex="2"/>
        </td>
    </tr>

    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="agency">{t}Agency{/t}</label>
        </td>
        <td style="padding:4px;" valign="top" nowrap="nowrap" >
            <input 	type="text" id="agency" name="agency" title="{t}Agency{/t}"
					class="required" style="width:95%" tabindex="3"
					{if is_object($article)}
						value="{$article->agency|clearslash|escape:"html"}"
						onblur="setTimeout(function(){ tinyMCE.get('summary').focus(); }, 200);"
					{else}
						value="nuevatribuna.es"
					{/if}
				/>
        </td>
    </tr>

    <tr>
        <td >
            <label for="summary">{t}Summary{/t}</label><br />
			{if is_object($article) && !$article->isClone()}
				<a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
					<img src="{$params.IMAGE_DIR}/users_edit.png" alt="" border="0" />
				</a>
			{/if}
        </td>
        <td >
            <textarea name="summary" id="summary" title="Resumen de la noticia"
                  onChange="countWords(this,document.getElementById('counter_summary'))"
                  onkeyup="countWords(this,document.getElementById('counter_summary'))" tabindex="4">{$article->summary|clearslash|escape:"html"}</textarea>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;">
            <label for="body">{t}Body{/t}</label>
        </td>
        <td style="padding-bottom: 5px; padding-top: 10px;" valign="top" nowrap="nowrap" colspan="2">
            <textarea name="body" id="body" title="Cuerpo de la noticia" style="width:98%; height:20em;" onChange="counttiny(document.getElementById('counter_body'));" tabindex="5">{$article->body|clearslash}</textarea>
        </td>
    </tr>
    <tr>
        <td></td>
        <td valign="top" align="left" colspan="2" >
            <div id="article_images" style="width:98%">
				{if isset($article)}
					{include file="article_images_edit.tpl"}
				{else}
					{include  file="article_images.tpl"}
				{/if}
            </div>
        </td>
    </tr>
	</tbody>
	<tfoot>
		<tr class="pagination">
			<td></td>
		</tr>
	</tfoot>
    </table>
</div>

{* Pestaña de parámetros de noticia ****************************************** *}
<div class="panel" id="edicion-extra" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
    <tbody>
    <tr>
        <td valign="top" align="right" style="padding:4px;" >
            <label for="starttime">{t}Publication start date:{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" >
            <div style="width:170px;">
                <input type="text" id="starttime" name="starttime" size="18"
                    title="Fecha inicio publicaci&oacute;n" value="{$article->starttime}" tabindex="-1" /></div>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" >
            <label for="endtime">{t}Publication end date:{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" >
            <div style="width:170px;">
                <input type="text" id="endtime" name="endtime" size="18"
                    title="Fecha fin publicaci&oacute;n" value="{$article->endtime}" tabindex="-1" /></div>

            <sub>{t}Server hour:{/t} {$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}</sub>
        </td>
    </tr>
    <tr>
        <td valign="top" align="right" style="padding:4px;" >
            <label for="description">{t}Description{/t}</label>
        </td>
        <td style="padding:4px;" nowrap="nowrap" >
            <textarea name="description" id="description"
                title="Descripción interna de la noticia" style="width:100%; height:8em;" tabindex="-1">{$article->description|clearslash}</textarea>
        </td>
    </tr>

    </tbody>
    </table>
</div>


{if $smarty.request.action eq 'read'}
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
            <td align="right">
             </td>
             <td align="right">
                 <a href="#" onClick="javascript:confirmarDelComment(this, '{$comments[c]->pk_comment}');" title="Eliminar">
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

     </td>
    </tbody>
    </table>
</div>


<div class="panel" id="opiniones-relacionadas" style="width:95%">
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
        <tbody><tr>
            <td colspan="2">

            </td>
        </tr>
        </tbody>
    </table>
</div>
{/if}


<div class="panel" id="contenidos-relacionados" style="width:95%">
	{include file="article_relacionados.tpl"}
</div>

{if is_object($article)}
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
                            <td width='120'>
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
{else}
	<div class="panel" id="elementos-relacionados" style="width:95%">
		<div style="padding:10px; width:90% margin:0 auto;">
			<h2>{t}Related contents in frontpage{/t}</h2>
			<div style="position:relative;" id="scroll-container2">
				<ul id="thelist2" style="padding: 4px; background: #EEEEEE"></ul>
			</div>
			<br>
			<h2>{t}Related contents in inner article:{/t}</h2>
			<div style="position:relative;" id="scroll-container2int">
				<ul id="thelist2int" style="padding: 4px; background: #EEEEEE"></ul>
			</div>

			<div class="p">
				<input type="hidden" id="ordenPortada" name="ordenArti" value="" size="140"></input>
				<input type="hidden" id="ordenInterior" name="ordenArtiInt" value="" size="140"></input>
			</div>
		</div>
	</div>
{/if}

{if isset($clones)}
<div class="panel" id="clones" style="width:95%">
    {include file="article_clones.tpl"}
</div>
{/if}

{if is_object($article) && $article->isClone()}
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
