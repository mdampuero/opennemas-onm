{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

{* LISTADO ******************************************************************* *}
{if !isset($smarty.post.action) || $smarty.post.action eq "list"}


    <ul class="tabs2" style="margin-bottom: 28px;">
       <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=todos" id="link_todos"  {if $category=='todos'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>TODOS</a>
        </li>
        <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=home" id="link_home" {if $category=='home'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>HOME</a>
        </li>
         <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=4" id="link_home" {if $category=='4'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>OPINION</a>
        </li>
        <script type="text/javascript">
                // <![CDATA[
                    {literal}
                             Event.observe($('link_todos'), 'mouseover', function(event) {
                             $('menu_subcats').setOpacity(0);
                             e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);

                            });
                             Event.observe($('link_home'), 'mouseover', function(event) {
                             $('menu_subcats').setOpacity(0);
                             e = setTimeout("show_subcat('{/literal}{$category}','{$home|urlencode}{literal}');$('menu_subcats').setOpacity(1);",1000);
                            });
                    {/literal}
                // ]]>
            </script>
       {*  <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=encuesta" {if $category=='encuesta' } style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>ENCUESTAS</a>
        </li>*}
        {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
    </ul>

<br />
{if $category neq "todos"}
    <ul class="tabs">
	<li>
		<a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=0" {if $comment_status==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}> Pendientes </font></a>
	</li>
	<li>
		<a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=1" {if $comment_status==1} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}> Publicados </font></a>
	</li>

	<li>
		<a href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&comment_status=2" {if $comment_status==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}> Rechazados </font></a>
	</li>
    </ul>
    <br /> <br />
{/if}

<br>

<div id="{$category}">

 {include file="botonera_up.tpl"}
 <br /><br />
    <table class="adminheading">
	    <tr>
		    <th nowrap>Comentarios pendientes de publicar</th>
	    </tr>
    </table>


    <table class="adminlist" border=0>
	<tr>
		<th  style='width:4%;'></th>
		<th  style='width:16%;'>Titulo</th>
		<th  style='width:25%;'>Comentario(50carac)</th>
        <th  style='width:10%;'>tipo</th>
		<th  style='width:25%;'>Contenido(50carac)</th>
		<th  style='width:10%;'>Autor</th>
		<th  style='width:6%;' align="center">IP</th>
		{if $category eq 'todos' || $category eq 'home'}
			<th align="center" style="width:5%;">Secci&oacute;n</th>
		{/if}
		<th  style='width:6%;' align="center">Fecha</th>
                <th style='width:100px;' align="center">Votos Pos/Neg</th>
		<th style='width:100px;' align="center">Publicar</th>
		<th style='width:60px;' align="center">Editar</th>
		<th style='width:60px;' align="center">Eliminar</th>
	  </tr>

     <div class='fisgona' id='fisgona' name='fisgona'></div>
        {* Provisional - comentarios en encuestas en la solapa todos *}

	{section name=c loop=$comments}
	<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
		<td style="font-size: 11px;width:4%;">
			<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$comments[c]->id}"  style="cursor:pointer;" >
		</td>
		<td style="padding:2px; font-size: 11px;width:16%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
			{$comments[c]->title|strip_tags|clearslash|truncate:50}
		</td>
		<td style="font-size: 11px;width:25%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)">
			{$comments[c]->body|strip_tags|clearslash|truncate:50}
		</td>
         {assign var=type value=$articles[c]->content_type}
        <td style="font-size: 11px;width:25%;" onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)">
             {$content_types[$type]}
        </td>
		<td style="padding:10px;font-size: 11px;width:25%;">
			<a style="cursor:pointer;"  onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->pk_comment}');new Effect.BlindUp('edicion-contenido'); new Effect.BlindDown('article-info'); return false;">
			{$articles[c]->title|strip_tags|clearslash}
			</a>
		</td>
		<td style="font-size: 11px;width:10%;">
			{$comments[c]->author|strip_tags} <br>
            {if preg_match('/@proxymail\.facebook\.com$/i', $comments[c]->email)}
                <span title="{$comments[c]->email}">via facebook</span>
            {else}
                {$comments[c]->email}
            {/if}
		</td>
		<td style="width:6%;font-size: 11px;" align="center">
				{$comments[c]->ip}
		</td>
		{if $category eq 'todos' || $category eq 'home'}
		<td style="width:6%;font-size: 11px;" align="center">
		 	{$articles[c]->category_name} {if $articles[c]->content_type==4}Opini&oacute;n{/if}
		</td>
		{/if}
		<td style="width:6%;font-size: 11px;" align="center">
			{$comments[c]->created}
		</td>
                <td style="width:100px;font-size: 11px;" align="center">
                           {$votes[c]->value_pos} /  {$votes[c]->value_pos}
                    </td>
		<td style="font-size: 11px;width:100px;" align="center">
                    {if $category eq 'todos' || $comments[c]->content_status eq 0}
                        <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Publicar">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                        <a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Rechazar">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                    {elseif $comments[c]->content_status eq 2}
                            <a class="unpublishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Publicar">
                                   </a>
                    {else}
                            <a class="publishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage}" title="Rechazar">
                                   </a>
                    {/if}
		</td>
		<td style="font-size: 11px;width:60px;" align="center">
			<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$comments[c]->id}');" title="Modificar">
				<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
		</td>
		<td style="font-size: 11px;width:60px;" align="center">
			<a href="#" onClick="javascript:confirmar(this, '{$comments[c]->id}');" title="Eliminar">
				<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
		</td>
	</tr>

	{sectionelse}
             {if $category eq 'todos'}
                 <tr>
                        <td align="center" colspan=10><h2 style="margin:50px">No hai ningun comentario <br/>pendiente de moderación</h2></td>
                </tr>
             {else}
                <tr>
                        <td align="center" colspan=10><br><br><p><h2><b>No hain ningún comentario guardado</b></h2></p><br><br></td>
                </tr>
             {/if}
	{/section}
 	<tr>
	    <td colspan="8" align="center">{$paginacion->links}</td>
	</tr>

    </table>
<br /><br />
{include file="botonera_down.tpl"}


</div>
{/if}

 {dialogo script="print"}
{* FORMULARIO PARA ENGADIR ************************************** *}

{* FORMULARIO PARA ACTUALIZAR *********************************** *}
{if isset($smarty.post.action) && $smarty.post.action eq "read"}

 {include file="botonera_up.tpl"}


<div id="edicion-contenido" style="width:95%;display:inline;">

<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="99%">
<tbody>

{*
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Noticia:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
			<h2><a style="cursor:pointer;"  onClick="new Effect.BlindUp('edicion-contenido'); new Effect.BlindDown('article-info'); return false;">{$article->title|clearslash}</a> <span style="font-size:9px;">(* Pinche sobre el titulo para ver la noticia.)</span></h2>

	</td>
</tr>
*}
<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">T&iacute;tulo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="title" name="title" title="Título de la noticia" onkeyup="countWords(this,document.getElementById('counter_title'))"
			value="{$comment->title|clearslash|escape:"html"}" class="required" size="100" />
		<input type="hidden" id="fk_content" name="fk_content" title="pk_article"
			value="{$comment->fk_content}" />
	</td>

	<td rowspan="5" valign="top"><b>
			<table style='background-color:#F5F5F5; padding:8px;' cellpadding="8">
			 <tr>
					<td valign="top" align="right" style="padding:4px;">
						<label for="title">Fecha:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" >
						<input type="text" id="date" name="date" title="author"
						value="{$comment->created}" class="required" size="20" readonly /></td>
				</tr>
			  <tr>
					<td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
			    		<label for="title"> Publicado: </label>
			    	</td>
						<td valign="top" style="padding:4px;" nowrap="nowrap">
							<select name="content_status" id="content_status" class="required">
								<option value="1" {if $comment->content_status eq 1} selected {/if}>Si</option>
								<option value="0" {if $comment->content_status eq 0} selected {/if}>No</option>
					  	   </select>
				</td>
				</tr>
				<tr>
					<td valign="top" align="right" style="padding:4px;" nowrap>
						<label for="title">IP:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" >
						<input type="text" id="ip" name="ip" title="author"
						value="{$comment->ip}" class="required" size="20" readonly /></td>
				</tr>
				<tr>
					<td valign="top" align="right" style="padding:4px;" nowrap>
						<label for="title">Nº Palabras t&iacute;tulo:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" >
						<input type="text" id="counter_title" name="counter_title" title="counter_title"
							value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
					</td>
				</tr>

				<tr>
					<td valign="top" align="right" style="padding:4px;" nowrap>
						<label for="title">Nº Palabras cuerpo:</label>
					</td>
					<td style="padding:4px;" nowrap="nowrap" >
						<input type="text" id="counter_body" name="counter_body" title="counter_body"
							value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
					</td>
				</tr>
				</table>
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="title">Autor:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<input type="text" id="author" name="author" title="author"
			value="{$comment->author|clearslash}" class="required" size="40" />
			<label for="title"> Email:</label><input type="text" id="email" name="email" title="email"
			value="{$comment->email|clearslash}" class="required" size="40" />
	</td>
</tr>

<tr>
	<td valign="top" align="right" style="padding:4px;" width="30%">
		<label for="body">Cuerpo:</label>
	</td>
	<td style="padding:4px;" nowrap="nowrap" width="70%">
		<textarea name="body" id="body"
			title="comment" style="width:96%; height:20em;">{$comment->body|clearslash}</textarea>
	</td>
</tr>
</tbody>
</table>

</div>

	<div id="article-info" style="width:95%;display:none;">
	 <table border="0" cellpadding="3" cellspacing="0">
			<tbody>
			<tr><td><label for="title">Comentario: </label></td>
			<td>
				<h2> <a style="cursor:pointer;"  onClick="new Effect.BlindDown('edicion-contenido'); new Effect.BlindUp('article-info'); return false;">
			 	{$comment->title|clearslash}</a></h2>
			</td></tr>
			<tr><td></td>
			  <td>
				 <table border="0" width="60%" style='background-color:#F5F5F5; padding:8px;' cellpadding="8">
				<tbody>
				<tr><td>
	 		 		<h3>{$article->subtitle|clearslash}</h3>
	 		 		 <h3 > {$article->agency|clearslash} - {$article->created|date_format:"%d/%m/%y "}</h3>

					<h2>{$article->title|clearslash}</h2>
					 <p>  <span style="float:left;"><img src="{$photo1->path_file}/{$photo1->name}" id="change1" name="{$article->img1}" border="0" width="180px" /></span>
					 {$article->summary|clearslash}
					    </p>

				 </td></tr>
				 <tr><td>
						<p>
							 <span style="float:right;">
							  <img src="{$photo2->path_file}/{$photo2->name}" id="change1" name="{$article->img2}" border="0" width="300px" /></span>
							{$article->body|clearslash}
						</p>


					</td>
				</tr>
				</tbody>
				</table>
			</td>
		</tr>
		</tbody>
		</table>
	</div>

{literal}
	<script>
		countWords(document.getElementById('title'), document.getElementById('counter_title'));
		countWords(document.getElementById('body'), document.getElementById('counter_body'));
	</script>
{/literal}

<script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
{literal}
<script type="text/javascript" language="javascript">
    tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
</script>

<script type="text/javascript" language="javascript">
    OpenNeMas.tinyMceConfig.advanced.elements = "body";
    tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
</script>
{/literal}

{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
