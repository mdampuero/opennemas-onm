{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="GET" name="formulario" id="formulario" {$formAttrs|default:""} >
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: {/t}{if $category eq '0'}{t}General statistics{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
        {if $category!=0}
        <ul class="old-button">
            <li>
				<a href="{$smarty.server.PHP_SELF}?action=upload&category={$category}&op=view" title="{t}Upload file{/t}">
					<img src="{$params.IMAGE_DIR}upload.png" border="0" /><br />
					{t}Upload file{/t}
				</a>
			</li>
            {acl isAllowed="FILES_DELETE"}
            <li>
                <a class="delChecked" data-controls-modal="modal-file-batchDelete" href="#" title="{t}Delete{/t}">
                    <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="{t}Delete{/t}" alt="{t}Delete{/t}" ><br />{t}Delete{/t}
                </a>
            </li>
            {/acl}
            {acl isAllowed="FILES_AVAILABLE"}
            <li>
                    <button value="batchnoFrontpage" name="buton-batchnoFrontpage" id="buton-batchnoFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
               </li>
               <li>
                   <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
            </li>
            {/acl}
        </ul>
        {/if}
    </div>
</div>
<div class="wrapper-content">
    <ul class="pills">
        <li>
            <a href="{$smarty.server.PHP_SELF}?action=list&category=0" id="link_global"  {if $category eq '0'}class="active"{/if}>{t}GLOBAL{/t}</a>
        </li>
        <li>
            <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;category=widget" {if $category eq 'widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
        </li>
        {include file="menu_categories.tpl" home="{$smarty.server.PHP_SELF}?action=list"}
    </ul>

    {render_messages}
	<div id="{$category}">
		{if $category eq '0'}
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
                        <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
						<th>{t}Title{/t}</th>
						<th>{t}Path{/t}</th>
						<th class="center" style="width:40px">{t}Availability{/t}</th>
                        {if $category!='widget'} <th class="center" style="width:35px;">{t}Favorite{/t}</th>{/if}
                        <th class="center" style="width:35px;">{t}Home{/t}</th>
                        <th class="center" style="width:40px">{t}Published{/t}</th>
						<th style="width:40px">{t}Actions{/t}</th>
					</tr>
				</thead>

				<tbody>
					{section name=c loop=$attaches}
					 <tr data-id="{$attaches[c]->id}">
                        <td class="center">
                                <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$attaches[c]->id}"  style="cursor:pointer;" >
                        </td>
                        <td>
							{$attaches[c]->title|clearslash}
						</td>
						<td>
                            <a href="{$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches[c]->path}" target="_blank">
							    {$smarty.const.INSTANCE_MEDIA}{$smarty.const.FILE_DIR}{$attaches[c]->path}
                            </a>
						</td>
						<td class="center">
							{if $status[c] eq 1}
								<img src="{$params.IMAGE_DIR}publish_g.png"  border="0" alt="Si"/>
							{else}
								<img src="{$params.IMAGE_DIR}icon_aviso.gif" border="0" alt="No" />
							{/if}
						</td>
                        {if $category != 'widget'}
                        <td class="center">
                            {acl isAllowed="FILE_AVAILABLE"}
                                {if $attaches[c]->favorite == 1}
                                   <a href="{$smarty.server.PHP_SELF}?id={$attaches[c]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                                {else}
                                    <a href="{$smarty.server.PHP_SELF}?id={$attaches[c]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="{t}Put in frontpage{/t}"></a>
                                {/if}
                            {/acl}
                        </td>
                        {/if}
                        <td class="center">
                            {acl isAllowed="FILE_AVAILABLE"}
                                {if $attaches[c]->in_home == 1}
                                   <a href="{$smarty.server.PHP_SELF}?id={$attaches[c]->id}&amp;action=change_inHome&amp;status=0&amp;category={$category}&amp;page={$page|default:0}" class="no_home" title="{t}Take out from home{/t}"></a>
                                {else}
                                    <a href="{$smarty.server.PHP_SELF}?id={$attaches[c]->id}&amp;action=change_inHome&amp;status=1&amp;category={$category}&amp;page={$page|default:0}" class="go_home" title="{t}Put in home{/t}"></a>
                                {/if}
                            {/acl}
                        </td>
                        <td align="center">
                            {acl isAllowed="FILE_AVAILABLE"}
                                {if $attaches[c]->available == 1}
                                    <a href="?id={$attaches[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Publicado">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                                    </a>
                                {else}
                                    <a href="?id={$attaches[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}&amp;category={$category}" title="Pendiente">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                                    </a>
                                {/if}
                            {/acl}
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
									<a class="del" data-controls-modal="modal-from-dom"
                               data-id="{$attaches[c]->id}"
                               data-title="{$attaches[c]->title|capitalize}"  href="#" >
                                        <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
								</li>
                                {/acl}
							</ul>
						</td>
					</tr>
					{sectionelse}
					<tr>
						<td class="empty" colspan="5">
							{t}There is not files available here.{/t}
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

        <input type="hidden" name="category" id="category" value="{$category}" />
        <input type="hidden" id="status" name="status" value="" />
        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

     <script>
        jQuery('#buton-batchnoFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "0");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
</div>
</form>
{include file="files/modals/_modalDelete.tpl"}
{include file="files/modals/_modalBatchDelete.tpl"}
{include file="files/modals/_modalAccept.tpl"}
{/block}
