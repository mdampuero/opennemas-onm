{extends file="base/admin.tpl"}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>

{* LISTADO ******************************************************************* *}
{if !isset($smarty.post.action) || $smarty.post.action eq "list"}


<ul class="tabs">
{*
{section name=as loop=$types_content}
    <li>
		 {assign var=ca value=`$types_content[as]`}
		<a href="litter.php?action=list&mytype={$ca}" {if $mytype==$ca} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{$types_content[as]}</a>
	</li>
{/section}
*}
<li><a href="litter.php?action=list&mytype=article" {if $mytype=='article'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Noticias</a></li>
<li><a href="litter.php?action=list&mytype=opinion" {if $mytype=='opinion'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Opiniones</a></li>
<li><a href="litter.php?action=list&mytype=advertisement" {if $mytype=='advertisement'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Publicidad</a></li>
<li><a href="litter.php?action=list&mytype=comment" {if $mytype=='comment'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Comentarios</a></li>
<li><a href="litter.php?action=list&mytype=album" {if $mytype=='album'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Albumes</a></li>
<li><a href="litter.php?action=list&mytype=photo" {if $mytype=='photo'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Fotografías</a></li>
<li><a href="litter.php?action=list&mytype=video" {if $mytype=='video'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Videos</a></li>
<li><a href="litter.php?action=list&mytype=attachment" {if $mytype=='attachment'} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>Ficheros</a></li>
</ul>

<br><br><br>
    {include file="botonera_up.tpl"}
<table class="adminheading">
	<tr>
		<th nowrap>Elementos en la papelera</th>
	</tr>
</table>

<div id="pagina">

<table class="adminlist">
   <tr>
   <th style="width:5%;"> &nbsp;</th>
	<th style="width:75%;" align='left'>T&iacute;tulo</th>
	<th style="width:5%;">Secci&oacute;n</th>
	<th  style="width:5%;">Visto</th>
	<th  style="width:10%;">Fecha</th>
	<th  style="width:5%;">Recuperar</th>
	<th  style="width:5%;">Eliminar</th>
  </tr>
  <tr><td colspan=7>
	   <div id="even" class="seccion" style="float:left;width:100%; border:1px solid gray;"> <br>
	   {assign var=aux value='2'}
	{section name=c loop=$litterelems}

		<table id='tabla{$aux}' name='tabla{$aux}' value="{$evenpublished[c]->id}" width="100%" class="tabla" style="text-align:center;padding:0px;">
		   <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;" >
			<td style="text-align: left;font-size: 11px;width:2%;">
				<input type="checkbox" class="minput"  id="selected{$smarty.section.c.iteration}" name="selected_fld[]" value="{$litterelems[c]->id}"  style="cursor:pointer;" onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
			</td>
			<td style="text-align: left;font-size: 11px;width:70%;" onClick="javascript:document.getElementById('selected{$smarty.section.c.iteration}').click();">
				{$litterelems[c]->title|clearslash}
			</td>
			<td style="text-align: center;font-size: 11px;width:5%;">
				 {$secciones[c]}
			</td>
			<td style="text-align: center;font-size: 11px;width:4%;">
				{$litterelems[c]->views}
			</td>
			<td style="text-align: center;width:11%;font-size: 11px;">
						{$litterelems[c]->created}
			</td>

			<td style="text-align: center;width:5%;">
					<a href="?id={$litterelems[c]->id}&amp;action=no_in_litter&amp;&amp;mytype={$mytype}&amp;page={$paginacion->_currentPage}" title="Recuperar">
						<img class="portada" src="{$params.IMAGE_DIR}trash_no.png" border="0" alt="Recuperar" width='24px' /></a>
			</td>
			<td style="text-align: center;width:5%;">
				<a href="#" onClick="javascript:vaciar(this, '{$litterelems[c]->id}');" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
			</td>
		</tr> </table>
	{sectionelse}
	<tr>
	<td align="center" colspan=6><br><br><p><h3><b>Ningun elemento en la papelera</b></h3></p><br><br></td>
	</tr>
	{/section}
	<td></tr>

	<tr>
	    <td colspan="5" align="center">{$paginacion->links}</td>
	</tr>

	</table>
	</div>
     </td></tr>
</table>

</div>


</div>
{/if}

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
