{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: General statistics{/t}</h2></div>
        {if $category!=0}
        <ul class="old-button">
            <li>
				<a href="{$smarty.server.PHP_SELF}?action=upload&category={$category}&op=view" title="{t}Upload file{/t}">
					<img src="{$params.IMAGE_DIR}upload.png" border="0" /><br />
					{t}Upload file{/t}
				</a>
			</li>
        </ul>
        {/if}
    </div>
</div>
<div class="wrapper-content">

	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""} >

		<ul class="pills">
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=list&category=0" id="link_global"  {if $category==0}class="active"{/if}>{t}GLOBAL{/t}</a>
			</li>
			{include file="menu_categories.tpl" home="{$smarty.server.PHP_SELF}?action=list"}
		</ul>
	<div id="{$category}">
		{if $category eq 0}
			<table class="listing-table">
				<thead>
					<tr>
						<th class="title" align="left">{t}Title{/t}</th>
						<th width="40px" align="left">{t}Files (#){/t}</th>
						<th width="40px" align="left">{t}Size (MB){/t}</th>
					</tr>
				</thead>
				<tbody>
					{section name=c loop=$categorys}
					<tr>
						<td style="width:300;">
							<a href="{$smarty.server.PHP_SELF}?action=list&category={$categorys[c]->pk_content_category}">{$categorys[c]->title|clearslash|escape:"html"}</a>
						</td>
						<td style="width:10%;" class="center">
							{$num_photos[c]}
						</td>
                        <td style="width:10%;" class="center">
                            {math equation="x / y" x=$size[c]|default:0 y=1024*100 format="%.2f"} MB
						</td>

					</tr>
                        {section name=su loop=$subcategorys[c]}
                        <tr>
                            <td style="padding: 5px 5px 5px 20px; width:300;">
                                <strong>=></strong> <a href="{$smarty.server.PHP_SELF}?action=list&category={$subcategorys[c][su]->pk_content_category}">{$subcategorys[c][su]->title|clearslash|escape:"html"}</a>
                            </td>
                            <td style="padding: 0px 10px; width:10%;" class="center">
                                {$num_sub_photos[c][$subcategorys[c][su]->pk_content_category]}
                            </td>
                            <td style="padding: 0px 10px; width:10%;" class="center">
                                {math equation="x / y" x=$sub_size[c][$subcategorys[c][su]->pk_content_category]|default:0 y=1024*100 format="%.2f"} MB</a>
                            </td>
                         </tr>
                        {/section}
					{/section}
					<tr>
						<td colspan="2">
						{section name=c loop=$num_especials}
							<table width="100%">
							<tr>
								<td >
									 <b> {$num_especials[c].title|upper|clearslash|escape:"html"}</b>
								</td>
								<td style="width:40px;" align="left">
									{$num_especials[c].num}
								</td>
							 </tr>

							</table>
						{/section}
					</tr>

				</tbody>

				<tfoot>
                    <tr>
                        <td class="left">
							<strong>{t}TOTAL{/t}</strong>
						</td>
						<td style="width:10%;" class="center">
							{$total_img}
						</td>
                        <td style="width:10%;" class="center">
                            {math equation="x / y" x=$total_size|default:0 y=1024*100 format="%.2f"} MB
						</td>
                    </tr>
				</tfoot>
			 </table>
		{else}
			{if isset($smarty.request.msg)}
			<div class="notice">
				{$smarty.request.msg}
			</div>
			{/if}

			<table class="listing-table">
				<thead>
					<tr>
						<th>{t}Title{/t}</th>
						<th>{t}Path{/t}</th>
						<th class="center" style="width:40px">{t}Availability{/t}</th>
						<th style="width:40px">{t}Actions{/t}</th>
					</tr>
				</thead>

				<tbody>
					{section name=c loop=$attaches}
					<tr {cycle values="class=row0,class=row1"}>
						<td>
							{$attaches[c]->title|clearslash}
						</td>
						<td>
							{$attaches[c]->path}
						</td>
						<td class="center">
							{if $status[c] eq 1}
								<img src="{$params.IMAGE_DIR}publish_g.png"  border="0" alt="Si"/>
							{else}
								<img src="{$params.IMAGE_DIR}icon_aviso.gif" border="0" alt="No" />
							{/if}
						</td>
						<td class="center">
							<ul class="action-buttons">
                                {acl isAllowed="FILE_UPDATE"}
								<li>
									<a href="{$smarty.server.PHP_SELF}?action=read&id={$attaches[c]->id}" title="Modificar"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
								</li>
                                {/acl}
                                {acl isAllowed="FILE_DELETE"}
								<li>
									<a href="#" onClick="javascript:delete_fichero('{$attaches[c]->id}',1);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
								</li>
                                {/acl}
							</ul>
						</td>
					</tr>
					{sectionelse}
					<tr>
						<td class="empty" colspan="5">
							{t}There is not files available here.{/t}>
						</td>
					</tr>
					{/section}
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7" class="pagination">
							{$pagination->links}
                            &nbsp;
						</td>
					</tr>
				</tfoot>
			</table>

			<div id="adjunto" class="adjunto"></div>

			</div>
		{/if}

		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" name="id" id="id" value="{$id|default:""}" />
	</form>
</div><!--fin wrapper-content-->
{/block}
