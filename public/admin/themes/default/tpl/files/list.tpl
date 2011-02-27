{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">

	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >

		<ul class="tabs">
			<li>
				<a href="{$_SERVER['PHP_SELF']}?action=list&category=0" id="link_global"  {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>{t}GLOBAL{/t}</a>
			</li>
			<script type="text/javascript">
			// <![CDATA[
			Event.observe($('link_global'), 'mouseover', function(event) {
				$('menu_subcats').setOpacity(0);
				e = setTimeout("show_subcat('{$category}','{$home|urlencode}');
				$('menu_subcats').setOpacity(1);",1000);
			});
			// ]]>
			</script>

			{include file="menu_categorys.tpl" home="{$_SERVER['PHP_SELF']}?action=list"}

		</ul>

		<br><br>
		<div id="menu-acciones-admin">
			<div style="float:left; margin:8px;"><h2>{if $category eq 0}{t}File manager :: Overview{/t}{else}{t}File manager :: Files in this category{/t}{/if}</h2></div>
			{if $category neq 0}
			<ul>
				<li>
					<a href="{$_SERVER['PHP_SELF']}?action=upload&category={$category}" title="{t}Upload file{/t}">
						<img src="{$params.IMAGE_DIR}upload.png" border="0" /><br />
						{t}Upload file{/t}
					</a>
				</li>
			</ul>
			{/if}
		</div>

	<div id="{$category}">
		{if $category eq 0}
			<table class="adminheading">
				<tr>
					<th>&nbsp;</th>
				</tr>
			</table>
			<table class="adminlist" id="tabla">
				<thead>
					<tr>
						<th width="300" class="title" align="left">{t}Title{/t}</th>
						<th width="10%" align="left">{t}Files (#){/t}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2">
							{section name=c loop=$categorys}
							<table width="100%" cellpadding=0 cellspacing=0  id="{$categorys[c]->pk_content_category}">
								<tr {cycle values="class=row0,class=row1"}>
									<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:300;">
										 <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
									</td>
									<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:10%;" align="left">
										{$num_photos[c]}</a>
									</td>
								</tr>
								<tr>
									<td colspan=2>
										{section name=su loop=$subcategorys[c]}
										<table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
												<tr {cycle values="class=row0,class=row1"}>
														<td style="padding: 0px 30px; height: 24px; font-size: 11px;width:300;">
																 <b>{$subcategorys[c][su]->title} </blockquote></b>
														</td>
													<td style="padding: 0px 10px; height: 20px;font-size: 11px;width:10%;" align="left">
														{$num_sub_photos[c][su]}
													</td>
												 </tr>
										</table>
										{/section}
									</td>
								</tr>
							</table>
							{/section}
					</tr>
					<tr>
						<td colspan="2">
						{section name=c loop=$num_especials}
							<table width="100%" cellpadding=0 cellspacing=0 >
							<tr {cycle values="class=row0,class=row1"}>
								<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:300;">
									 <b> {$num_especials[c].title|upper|clearslash|escape:"html"}</b>
								</td>
								<td style="padding: 0px 10px; height: 24px;font-size: 11px;width:10%;" align="left">
									{$num_especials[c].num}</a>
								</td>
							 </tr>

							</table>
						{/section}
					</tr>

				</tbody>

				<tfoot>
					<tr>
						<td colspan="7" class="pagination">
							{$pagination->links}
						</td>
					</tr>
				</tfoot>
			 </table>
		{else}
			{if {$smarty.request.msg}}
			<div class="notice">
				{$smarty.request.msg}
			</div>
			{/if}
			<table class="adminheading">
				<tr>
					<th>{t}File manager{/t}</th>
				</tr>
			</table>

			<table class="adminlist">
				<thead>
					<tr>
						<th class="title">{t}Title{/t}</th>
						<th class="title">{t}Path{/t}</th>
						<th align="center">{t}Availability{/t}</th>
						<th align="center">{t}Edit{/t}</th>
						<th align="center">{t}Delete{/t}</th>
					</tr>
				</thead>

				<tbody>
					{section name=c loop=$attaches}
					<tr {cycle values="class=row0,class=row1"}>
						<td style="padding:10px;font-size: 11px;">
							{$attaches[c]->title|clearslash}
						</td>
						<td style="padding:10px;font-size: 11px;">
							{$attaches[c]->path}
						</td>
						<td style="padding:10px;font-size: 11px;width: 84px;" align="center">
							{if $status[c] eq 1}
								<img src="{$params.IMAGE_DIR}publish_g.png"  border="0" alt="Si"/>
							{else}
								<img src="{$params.IMAGE_DIR}icon_aviso.gif" border="0" alt="No" />
							{/if}
						</td>
						<td style="padding:10px;font-size: 11px;width: 84px;" align="center">
								<a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$attaches[c]->id}');" title="Modificar"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
						</td>
						<td style="padding:10px;font-size: 11px;width: 84px;" align="center">
							<a href="#" onClick="javascript:delete_fichero('{$attaches[c]->id}',1);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
						</td>
					</tr>
					{sectionelse}
					<tr>
						<td align="center" colspan="5">
							<div style="margin:50px">{t}There is not files available here.{/t}</div>
						</td>
					</tr>
					{/section}
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" class="pagination">
							{$pagination->links}
						</td>
					</tr>
				</tfoot>
			</table>

			<div id="adjunto" class="adjunto"></div>

			</div>
		{/if}

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id}" />
	</form>
</div><!--fin wrapper-content-->
{/block}
