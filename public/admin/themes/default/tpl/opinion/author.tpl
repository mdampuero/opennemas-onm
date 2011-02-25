{extends file="base/admin.tpl"}


{block name="content"}
<div class="wrapper-content">

{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
	{include file="botonera_up.tpl"}
	<table class="adminheading">
		<tr align="right">
			<th>
				Seleccione autor:
				<select name="autores" id="autores" class="" onChange="window.location='{$smarty.server.SCRIPT_NAME}?action=read&id='+this.options[this.selectedIndex].value;">
					<option> -- </option>
					{section name=as loop=$authors_list}
						<option value="{$authors_list[as]->pk_author}" >{$authors_list[as]->name}</option>
					{/section}
				</select>
			</th>
		</tr>
	</table>
	<table border="0" cellpadding="4" cellspacing="0" class="adminlist" width="600">
		<thead>
			<tr>
				<th class="title" style="align:left;padding:10px;width:30%;">{t}Author name{/t}</th>
				<th class="title" style="padding:10px;width:20%;">{t}Genre{/t}</th>
{*---------				<th class="title" style="padding:10px;width:20%;">{t}Politic sign{/t}</th> ------------*}
				<th class="title" style="padding:10px;width:20%;">{t}Condition{/t}</th>
				<th class="title" style="padding:10px;width:10%;">{t}Photos (#){/t}</th>
				<th class="title" style="padding:10px;width:10%;align:right;">{t}Edit{/t}</th>
				<th class="title" style="padding:10px;width:10%;align:right;">{t}Delete{/t}</th>
			</tr>
		</thead>
		{section name=c loop=$authors}
			<tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
				<td style="padding:10px;">
						{$authors[c]->name}&nbsp;&nbsp;{*if $authors[c]->fk_user != 0}(usuario){/if*}
				</td>
				<td style="padding:10px;">
								{$authors[c]->gender}
{*				</td>
				<td style="padding:10px;">
						{$authors[c]->politics}
				</td> -----------*}
				<td style="padding:10px;">
						{$authors[c]->condition}
				</td>
				<td style="padding:10px;">
						{$authors[c]->num_photos}
				</td>
				<td style="padding:10px;align:center;">
					<a href="{$_SERVER['PHP_SELF']}?action=read&id={$authors[c]->pk_author}" title="Modificar">
						<img src="{$params.IMAGE_DIR}edit.png" border="0" />
					</a>
				</td>
				<td style="padding:10px;align:center;">
					<a href="#" onClick="javascript:confirmar(this, {$authors[c]->pk_author});" title="Eliminar">
						<img src="{$params.IMAGE_DIR}trash.png" border="0" />
					</a>
                                      
				</td>
			</tr>
            
		{sectionelse}
			<tr>
				<td align="center"><b>{t}There is no available authors{/t}</b></td>
			</tr>
        {/section}
            <input type="hidden" id="action" name="action" value="">
            <input type="hidden" id="id" name="id" value={$id}>
        </form>
		<tfoot>
			<tr>
				<td colspan="7" align="center">
					{$paginacion->links}
				</td>
			</tr>
		</tfoot>
    </table>

{/if}


{* FORMULARIO PARA ENGADIR OU MODIFICAR  ************************************** *}
{if isset($smarty.request.action) && (($smarty.request.action eq "new") || ($smarty.request.action eq "read"))}
	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
	{include file="botonera_up.tpl"}

	<table class="adminheading">
		<tr align="right"><td>&nbsp;</td></tr>
	</table>
	<table  class="fuente_cuerpo adminlist">
		<tbody>

			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="name">{t}Name{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap" width="60%">
					<input type="text" id="name" name="name" title="{t}Author name{/t}"
						value="{$author->name}" class="required"  size="50"/>
				</td>
			</tr>
 			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="phone">{t}Condition{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="condition" name="condition" title="{t}Condition{/t}" value="{$author->condition}"  size="50"/>
				</td>
			</tr>
			    
 {* 			<tr>
				<td valign="top" align="right" style="padding:4px;">
					<label for="title">{t}Sex:{/t}</label>
				</td>
					<td style="padding:4px;" nowrap="nowrap">
						 <select name="gender" id="gender" class="required">
							<option value="Mujer" {if $author->gender eq 'Mujer'} selected {/if}>{t}Women{/t}</option>
							<option value="Hombre" {if $author->gender eq 'Hombre'} selected {/if}>{t}Men{/t}</option>
						</select>
					</td>
			</tr> *}

			<tr>
                <td valign="top" align="right" style="padding:4px;">
					<label for="phone">{t}Blog name:{/t}</label>
				</td>
                <td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="blogname" name="blogname" title="{t}Blog name{/t}" value="{$author->condition}"  size="50"/>
				</td>
{*				<td style="padding:4px;" nowrap="nowrap">
					<select name="politics" id="politics" class="required" title="Tendencia politica">
						<option value="Progresista" {if $author->politics eq 'Progresista'} selected {/if}>{t}Progresist{/t}</option>
						<option value="Conservador" {if $author->politics eq 'Conservador'} selected {/if}>{t}Conservative{/t}</option>
						<option value="Izquierdas" {if $author->politics eq 'Izquierdas'} selected {/if}>{t}Left-wind{/t}</option>
						<option value="Derechas" {if $author->politics eq 'Derechas'} selected {/if}>{t}Right-wind{/t}</option>
						<option value="Centro" {if $author->politics eq 'Centro'} selected {/if}>{t}Center-wind{/t}</option>
						<option value="Comunista" {if $author->politics eq 'Comunista'} selected {/if}>{t}Comunist{/t}</option>
					 </select>
				</td>*}
			</tr> 
            <tr>
                <td valign="top" align="right" style="padding:4px;">
					<label for="phone">{t}Blog url:{/t}</label>
				</td>
                <td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="blogurl" name="blogurl" title="{t}Blog url{/t}" value="{$author->gender}"  size="50"/>
				</td>
            </tr>

 {*			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<label for="phone">{t}Birthday:{/t}</label>
				</td>
				<td style="padding:4px;" nowrap="nowrap">
					<input type="text" id="date_nac" name="date_nac" size="18" title="{t}Birthday{/t}" value="{$author->date_nac}" /><button id="triggerend">...</button>
				</td>
			</tr> *}

			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">
					<b>{t}Author photos{/t}</b>
				</td>
				<td style="padding:4px;">
					<div id="contenedor" name="contenedor" style="display:none; "> </div>
					<div class="photos" >
						 <ul id='thelist'  class="gallery_list">
							{section name=as loop=$photos}
							<li id='{$photos[as]->pk_img}'>
								<div style="float: left;width:90px;">
									<a><img src="{$MEDIA_IMG_PATH_URL}{$photos[as]->path_img}" id="{$photos[as]->pk_img}" width="67"  border="1" /></a>
									<br>
									{$photos[as]->description}
								</div>
								<a href="#" onclick="javascript:del_photo('{$photos[as]->pk_img}');" title="{t}Delete photo{/t}">
									<img src="{$params.IMAGE_DIR}iconos/eliminar.gif" border="0" align="absmiddle" />
								</a>&nbsp;
							</li>
							{/section}
						 </ul>
					</div>
                    <input type="hidden" id="action" name="action" value="">
					<input type="hidden" id="del_img" name="del_img" value="">
					<input type="hidden" id="fk_author_img" name="fk_author_img" value="" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="right" style="padding:4px;" width="40%">{t}Upload more files{/t}</td>
				<td>
					<div id="iframe" style="display: inline;">
						<iframe src="newPhoto.php?nameCat=authors&category=7" style=" background:#fff; height:300px; width:100%" align="center" frameborder="0" framespacing="0" scrolling="none" border="0"></iframe>
					</div>
				</td>
			</tr>
			<div id="photograph" style="width:80%; margin:0 auto;">
			</div>
		</tbody>

		<tfoot>
			<tr class="pagination">
				<td colspan=2></td>
			</tr>
		</tfoot>
	</table>


    {*dhtml_calendar inputField="date_nac" button="triggerend" singleClick=true ifFormat="%Y-%m-%d" firstDay=1 align="CR"*}

	</form>
	<style type="text/css">
		.gallery_list {
			width:auto !important;
		}
	</style>
{/if}
</div><!--fin wrapper-content-->
{/block}
