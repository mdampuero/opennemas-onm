{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsarticle.js" language="javascript"}
    {script_tag src="/editables.js" language="javascript"}
    {script_tag src="/utilsGallery.js" language="javascript"}
{/block}

{block name="header-css" append}
	<style type="text/css">
	label {
		display:block;
		color:#666;
		text-transform:uppercase;
	}
	.utilities-conf label {
		text-transform:none;
	}
	</style>
{/block}

{block name="footer-js" append}
<script type="text/javascript" language="javascript">
document.observe('dom:loaded', function() {
	if($('title')){
		new OpenNeMas.Maxlength($('title'), {});
		$('title').focus(); // Set focus first element
	}
	getGalleryImages('listByCategory','{$category}','','1');
	getGalleryVideos('listByCategory','{$category}','','1');
});

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
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	{include file="article/partials/_menu.tpl"}
	<div class="wrapper-content">
          
        {render_messages}
          
		{if is_object($article) && $article->isClone()}
		<div class="notice">
			{assign var="original" value=$article->getOriginal()}
            This article was <strong>cloned</strong>. <br> For editing the content you must go to
            <a href="article.php?action=read&id={$original->id}">the original article</a>.
		</div>
		{/if}

		{* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
		<ul id="tabs">
			<li>
				<a href="#edicion-contenido">{t}Article content{/t}</a>
			</li>
			<li>
				<a href="#edicion-extra">{t}Article parameters{/t}</a>
			</li>
<!--            <li>
				<a href="#avanced-custom">{t}Article customize{/t}</a>
			</li>-->
      
			{if isset($article) && is_object($article) && !$article->isClone()}
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
			{if isset($article) && is_object($article) && isset($clones)}
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
				<td style="padding:4px;" valign="top" >
					<label for="title">{t}Frontpage title:{/t}</label>
					<input type="text" id="title" name="title" title="Título de la noticia en portada"
						value="{$article->title|clearslash|escape:"html"|default:""}" class="required" style="width:98%" maxlength="256" onChange="countWords(this,document.getElementById('counter_title'));"
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
								<td align="right">
								<label for="category">{t}Section:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top">
									<select name="category" id="category" class="validate-section" onChange="get_tags($('title').value);"  tabindex="8">
										<option value="20" {if !isset($category)}selected{/if} name="{t}Unknown{/t}" >{t}Unknown{/t}</option>
										{section name=as loop=$allcategorys}
										{acl hasCategoryAccess=$allcategorys[as]->pk_content_category}
										<option value="{$allcategorys[as]->pk_content_category}" {if $category eq $allcategorys[as]->pk_content_category || $article->category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
										{section name=su loop=$subcat[as]}
											{if $subcat[as][su]->internal_category eq 1}
												<option value="{$subcat[as][su]->pk_content_category}"
												{if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
											{/if}
										{/section}
										{/acl}
										{/section}
									</select>
								</td>
							</tr>
							{if $smarty.session.desde != 'list_hemeroteca'}
							 <tr>
								<td align="right">
									<label for="with_comment">{t}Allow coments{/t}</label>
								</td>
								<td  style="text-align:left;vertical-align:top">
									<input type="checkbox" {if (isset($article) && $article->with_comment eq 1)}checked{/if} name="with_comment" id="with_comment" value=1 tabindex="9"/>
								</td>
							</tr>
							 <tr>
								<td align="right">
									<label for="available">{t}Available:{/t}</label>
								</td>

								<td  style="text-align:left;vertical-align:top">
									<input type="checkbox" {if (isset($article) && $article->content_status eq 1)}checked{/if}  name="content_status" id="content_status" value=1 tabindex="10"/>
								</td>
							</tr>
							<tr>
								<td align="right">
									<label for="frontpage">{t}Put in section frontpage:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top">
									<input type="checkbox"  name="frontpage" {if (isset($article) && $article->frontpage eq 1)}checked{/if} id="frontpage" value=1 tabindex="11"/>
								</td>
							</tr>
							{if ($article->in_home neq 1)}
							<tr>
								<td valign="top"  align="right">
									<label for="in_home">{t}Suggest for frontpage:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top">
															<input type="checkbox"  name="in_home" {if (isset($article) && $article->in_home eq 2)}checked{/if} id="in_home" value=2 tabindex="7"/>
								</td>
							</tr>
							{/if}
							{else} {* else if not list_hemeroteca *}
							<tr>
								<td valign="top"  align="right">
															<label for="content_status">{t}Archived:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top">
									<input type="checkbox" name="content_status" {if (isset($article) && $article->content_status == 0)}checked{/if} value="0" id="content_status"/>
									<input type="hidden" id="columns" name="columns"  value="{$article->columns}" />
									<input type="hidden" id="home_columns" name="home_columns"  value="{$article->home_columns}" />
									<input type="hidden" id="with_comment" name="with_comment"  value="{$article->with_comment}" />
									<input type="hidden" id="available" name="available"  value="{$article->available}" />
									<input type="hidden" id="in_home" name="in_home"  value="{$article->in_home}" />
								</td>
							</tr>
							{/if}
                            <tr>
                                <td colspan="2">
                                    <h3>Statistics</h3>
                                </td>
                            </tr>
							<tr>
								<td align="right">
									<label for="counter_title">{t}Frontpage title:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top;" >
									<input type="text" id="counter_title" name="counter_title" title="counter_title" disabled=disabled
										value="0" onkeyup="countWords(document.getElementById('title'),this)" tabindex="-1"/> {t}words{/t}
								</td>
							</tr>
							<tr>
								<td align="right">
									<label for="counter_title">{t}Inner title{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top;" >
									<input type="text" id="counter_title_int" name="counter_title_int" title="counter_title_int" disabled=disabled
										value="0" onkeyup="countWords(document.getElementById('title_int'),this)" tabindex="-1"/> {t}words{/t}
								</td>
							</tr>
							<tr>
								<td align="right">
									<label for="counter_subtitle">{t}Pretitle:{/t}</label>
								</td>
								<td style="text-align:left;vertical-align:top;" >
									<input type="text" id="counter_subtitle" name="counter_subtitle" title="counter_subtitle" disabled=disabled
										value="0" onkeyup="countWords(document.getElementById('subtitle'),this)" tabindex="-1"/> {t}words{/t}
								</td>
							</tr>
							<tr colspan=2>
								<td align="right">
									<label for="counter_summary">{t}Summary:{/t}</label>
								</td>
								<td  style="text-align:left;vertical-align:top">
									<input type="text" id="counter_summary" name="counter_summary" title="counter_summary" disabled=disabled
										value="0"
										   onChange="countWords(document.getElementById('summary'),this)"
										   onkeyup="countWords(document.getElementById('summary'),this)" tabindex="-1"/> {t}words{/t}
								</td>
							</tr>
							<tr>
								<td align="right">
									<label for="counter_body">{t}Body:{/t}</label>
								</td>
								<td  style="text-align:left;vertical-align:top" >
									<input type="text" id="counter_body" name="counter_body" title="counter_body" disabled=disabled
										value="0" size="3" onChange="counttiny(document.getElementById('counter_body'));" onkeyup="counttiny(document.getElementById('counter_body'));" tabindex="-1"/>
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
				<td style="padding:4px;" valign="top" >
					<label for="title">{t}Inner title:{/t}</label>
					<input 	type="text" id="title_int" name="title_int" title="{t}Inner title:{/t}"
							value="{$article->title_int|clearslash|escape:"html"}" class="required" style="width:98%"
							maxlength="256"
							onChange="countWords(this,document.getElementById('counter_title_int'));get_tags(this.value);"
							onkeyup="countWords(this,document.getElementById('counter_title_int'))"
							tabindex="2"/>

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
				<td style="padding:4px;" >
					<label for="metadata">{t}Keywords{/t} <small>{t}(Separated by commas){/t}</small></label>
					<input type="text" id="metadata" name="metadata"
						   {if isset($article) && is_object($article)}
						   value="{$article->metadata}"
						   onChange="search_related('{$article->pk_article|default:""}',$('metadata').value);"
						   {/if}
						   style="width:98%" title="Metadatos" tabindex="3"/>

				</td>
			</tr>

			<tr>
				<td style="padding:4px;" valign="top" >
					<label for="subtitle">{t}Pretitle{/t}</label>
					<input type="text" id="subtitle" name="subtitle" title="antetítulo" style="width:98%"
						value="{$article->subtitle|upper|clearslash|escape:"html"}" onChange="countWords(this,document.getElementById('counter_subtitle'))" onkeyup="countWords(this,document.getElementById('counter_subtitle'))" tabindex="4"/>
				</td>
			</tr>

			<tr>
				<td style="padding:4px;" valign="top" >
					<label for="agency">{t}Agency{/t}</label>
					<input 	type="text" id="agency" name="agency" title="{t}Agency{/t}"
							class="required" style="width:98%" tabindex="5"
							{if is_object($article)}
								value="{$article->agency|clearslash|escape:"html"}"
								onblur="setTimeout(function(){ tinyMCE.get('summary').focus(); }, 200);"
							{else}
								value="{setting name=site_agency}"
							{/if}
						/>
				</td>
			</tr>

			<tr>
				<td style="padding:4px;">
					<label for="summary">
						{t}Summary{/t}
						{if is_object($article) && !$article->isClone()}
							<a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('summary');return false;" title="Habilitar/Deshabilitar editor">
								<img src="{$params.IMAGE_DIR}/users_edit.png" alt="" border="0" />
							</a>
						{/if}
					</label>
					<textarea tabindex="6" name="summary" id="summary" title="Resumen de la noticia" style="width:98%; min-height:70px;"
						  onChange="countWords(this,document.getElementById('counter_summary'))"
						  onkeyup="countWords(this,document.getElementById('counter_summary'))">{$article->summary|clearslash|escape:"html"}</textarea>
				</td>
			</tr>
			<tr style="padding:4px;">
				<td style="padding-bottom: 5px; padding-top: 10px;" valign="top" colspan="2">
					<label for="body">{t}Body{/t}
					{if is_object($article) && !$article->isClone()}
						<a href="#" onclick="OpenNeMas.tinyMceFunctions.toggle('body');return false;" title="Habilitar/Deshabilitar editor">
							<img src="{$params.IMAGE_DIR}/users_edit.png" alt="" border="0" />
						</a>
					{/if}
					</label>
					<textarea tabindex="7"name="body" id="body" title="Cuerpo de la noticia"
							  style="width:100%; height:20em;"
							  onChange="counttiny(document.getElementById('counter_body'));" >{$article->body|clearslash}</textarea>
				</td>
			</tr>
			<tr style="padding:4px;">
				<td valign="top" align="left" colspan="2" >
					<div id="article_images">
						{include  file="article/partials/_images.tpl"}
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
		<div class="panel" id="edicion-extra" style="width:98%">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
			<tbody>
             <tr>
                <td valign="top" align="right" style="padding:4px;" >
						<label for="slug">{t}Slug{/t}</label>
				</td>
				<td style="padding:4px;" valign="top" >				
					<input 	type="text" id="slug" name="slug" title="{t}slug{/t}" size="90"
							style="width:98%" maxlength="256" tabindex="5"
							{if is_object($article)}
								value="{$article->slug|clearslash|escape:"html"}"
							{else}
								value=""
							{/if}
						/>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;" >
					<label for="starttime">{t}Publication start date:{/t}</label>
				</td>
				<td style="padding:4px;" >
					<div style="width:170px;">
						<input type="text" id="starttime" name="starttime" size="18"
							title="Fecha inicio publicaci&oacute;n" value="{$article->starttime}" tabindex="-1" /></div>
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;" >
					<label for="endtime">{t}Publication end date:{/t}</label>
				</td>
				<td style="padding:4px;" >
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
				<td style="padding:4px;" >
					<textarea name="description" id="description"
						title="Descripción interna de la noticia" style="width:100%; height:8em;" tabindex="-1">{$article->description|clearslash}</textarea>
				</td>
			</tr>

			</tbody>
			</table>
		</div>
{*
        <div class="panel" id="avanced-custom" style="width:98%">
			<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600">
			<tbody>
            <tr>
				<td style="padding:4px;" valign="top" >
					<label for="agency">{t}Agency bulletin{/t}</label>
                </td>
				<td style="padding:4px;" >
					<input 	type="text" id="agencyWeb" name="params['agencyWeb']" title="{t}Agency{/t}"
							 style="width:98%" tabindex="5"
							{if is_object($article)}
								value="{$article->params['agencyWeb']|clearslash|escape:"html"}"
							{else}
								value="{setting name=site_agency}"
							{/if}
						/>
				</td>
			</tr>
            <tr><td colspan="2"> <hr/> </td></tr>
            <tr><td colspan="2"> <h2> {t}Customize for home{/t} </h2> </td></tr>
             <tr>
				<td style="padding:4px;" valign="top" >
					<label for="agency">{t}Home Title{/t}</label>
                </td>
				<td style="padding:4px;" >
					<input 	type="text" id="titleHome" name="params['titleHome']" title="{t}Title for Home{/t}"
							style="width:98%" tabindex="5"
							{if is_object($article)}
								value="{$article->params['titleHome']|clearslash|escape:"html"}"
							{else}
								value=""
							{/if}
						/>
				</td>
            </tr>
            <tr>
				<td style="padding:4px;" valign="top" >
                    <label>{t}Size for title{/t}</label>
                </td>
				<td style="padding:4px;" >
                        <select name="title_size" id="title_size" class="required" >
                            <option value="22" {if $article->title_size eq "22"} selected{/if}>22px</option>
                            <option value="24" {if $article->title_size eq "24"} selected{/if}>24px</option>
                            <option value="26" {if $article->title_size eq "26" || !$article->title_size} selected{/if}>26px</option>
                            <option value="28" {if $article->title_size eq "28"} selected{/if}>28px</option>
                            <option value="30" {if $article->title_size eq "30"} selected{/if}>30px</option>
                            <option value="32" {if $article->title_size eq "32"} selected{/if}>32px</option>
                            <option value="34" {if $article->title_size eq "34"} selected{/if}>34px</option>
                            <option value="36" {if $article->title_size eq "36"} selected{/if}>36px</option>
                            <option value="38" {if $article->title_size eq "38"} selected{/if}>38px</option>
                            <option value="40" {if $article->title_size eq "40"} selected{/if}>40px</option>
                            <option value="42" {if $article->title_size eq "42"} selected{/if}>42px</option>
                        </select>
                </td>
			</tr>
            <tr>
				<td style="padding:4px;" valign="top" >
					<label for="agency">{t}Home Subtitle{/t}</label>
                </td>
				<td style="padding:4px;" >
					<input 	type="text" id="titleHome" name="params['subtitleHome']" title="{t}Title for Home{/t}"
							 style="width:98%" tabindex="5"
							{if is_object($article)}
								value="{$article->params['subtitleHome']|clearslash|escape:"html"}"
							{else}
								value=""
							{/if}
						/>
				</td>
			</tr>
			 
			<tr>
				<td valign="top" align="right" style="padding:4px;" >
					<label for="description">{t}Home summary{/t}</label>
				</td>
				<td style="padding:4px;" >
					<textarea name="sumary_home" id="sumary_home"
						title="Resumen noticia para home" style="width:100%; height:8em;" tabindex="-1">{$article->params['summary_home']|clearslash}</textarea>
				</td>
			</tr>
             <tr>
				<td valign="top" align="right" style="padding:4px;" >
					<label for="description">{t}Image position{/t}</label>
				</td>
				<td style="padding:4px;" >

                <select name="img_pos" id="img_pos" class="required">
                    <option value="left" {if $article->img_pos eq "left" || !$article->img_pos} selected{/if}>Izquierda</option>
                    <option value="right" {if $article->img_pos eq "right"} selected{/if}>Derecha</option>
                    <option value="none" {if $article->img_pos eq "none"} selected{/if}>Justificada(300px)</option>
                </select>
                </td>
            </tr>
             <tr>
				<td valign="top" align="right" style="padding:4px;" >
					<label for="description">{t}Image for home{/t}</label>
				</td>
				<td style="padding:4px;" >
                    {include file="article/partials/_load_image.tpl"}
                </td>
            </tr>
          
            
			</tbody>
			</table>
		</div>
*}
    
		{if $smarty.request.action eq 'read'}
		<div class="panel" id="comments" style="width:98%">
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
			
			</tbody>
			</table>
		</div>

 
		{/if}


		<div class="panel" id="contenidos-relacionados" style="width:98%">
			{include file="article/partials/_related.tpl"}
		</div>

		{if isset($article) && is_object($article)}
		<div class="panel" id="elementos-relacionados" style="width:98%">
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
											<img src="{$params.IMAGE_DIR}btn_no.png" border="0" /> </a>
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
										<img src="{$params.IMAGE_DIR}btn_no.png" border="0" /> </a>
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
			<div class="panel" id="elementos-relacionados" style="width:98%">
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
		<div class="panel" id="clones" style="width:98%">
			{include file="article/partials/_clones.tpl"}
		</div>
		{/if}

		{if isset($article) && is_object($article) && $article->isClone()}
		{* Disable fields via javascript if $article->isClone() *}
		<script type="text/javascript">
		(function() {
			var elements = ['title', 'description', 'subtitle', 'metadata', 'agency'];
			elements.each(function(item){
				$(item).disabled = true;
				$(item).setAttribute('disabled', 'disabled');
			});
		}());
		</script>
		{/if}

        {script_tag src="/tiny_mce/opennemas-config.js"}
		<script type="text/javascript" language="javascript">
			tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );

			{if isset($article) && $article->isClone()}
				OpenNeMas.tinyMceConfig.simple.readonly   = 1;
				OpenNeMas.tinyMceConfig.advanced.readonly = 1;
			{/if}

			OpenNeMas.tinyMceConfig.simple.elements = "summary";
			tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

			OpenNeMas.tinyMceConfig.advanced.elements = "body";
			tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
		</script>

		<div id="reloadPreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
			<img src="{$params.IMAGE_DIR}loading.gif" border="0" align="absmiddle" />
			<span id="reloadPreviewText"></span>
		</div>
		<div id="savePreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
			<img src="{$params.IMAGE_DIR}btn_filesave.png" border="0" align="absmiddle" />
			<span id="savePreviewText"></span>
		</div>

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
	</div>
</form>
{/block}
