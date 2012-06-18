{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="GET" name="formulario" id="formulario" {$formAttrs|default:""} >
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Files manager :: {/t}{if $category eq '0'}{t}General statistics{/t}{else}{$datos_cat[0]->title}{/if}</h2></div>
        {if $category!=0}
        <ul class="old-button">
            <li>
				<a href="{$smarty.server.PHP_SELF}?action=upload&amp;category={$category}&amp;op=view" title="{t}Upload file{/t}">
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
            <a href="{url name=admin_files_statistics}" id="link_global" {if $category eq '0'}class="active"{/if}>{t}Statistics{/t}</a>
        </li>
        <li>
            <a href="{url name=admin_files_widget}" {if $category eq 'widget'}class="active"{/if}>{t}WIDGET HOME{/t}</a>
        </li>
        {include file="menu_categories.tpl" home="{url name=admin_files action=list}"}
    </ul>

    {render_messages}

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
				<th style="width:100px">{t}Actions{/t}</th>
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
                    <div class="btn-group">
                        {acl isAllowed="FILE_UPDATE"}
                            <a class="btn"  href="{url name=admin_files_show id=$attaches[c]->id}" title="{t}Edit file{/t}">
                                <i class="icon-pencil"></i> Edit
                            </a>
                        {/acl}
                        {acl isAllowed="FILE_DELETE"}
                            <a class="btn btn-danger" data-controls-modal="modal-from-dom"
                               data-id="{$attaches[c]->id}"
                               data-title="{$attaches[c]->title|capitalize}"  href="#" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                        {/acl}
                    </div>
				</td>
			</tr>
			{sectionelse}
			<tr>
				<td class="empty" colspan="8">
					{t}There is not files available here.{/t}
				</td>
			</tr>
			{/section}
		</tbody>
		<tfoot>
			<tr>
				<td colspan="8" class="pagination">
					{$pagination->links}
                    &nbsp;
				</td>
			</tr>
		</tfoot>
	</table>

	<div id="adjunto" class="adjunto"></div>

	</div>

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
