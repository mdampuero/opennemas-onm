{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    $('[rel=tooltip]').tooltip({ placement : 'bottom' });
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
    .table td { line-height:14px; }
    .tooltip-inner {
        max-width:500px !important;
        text-align: justify;
    }
</style>
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {t}Comments{/t}
                </h2>
            </div>
            <ul class="old-button">
                {acl isAllowed="COMMENT_DELETE"}
                <li>
                   <a class="delChecked" data-controls-modal="modal-comment-batchDelete" href="#" title="{t}Delete{/t}" alt="{t}Delete{/t}">
                       <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />Eliminar
                   </a>
                </li>
                {/acl}
                {acl isAllowed="COMMENT_AVAILABLE"}
                {if $status neq '2'}
                <li>
                    <button name="status" value="0" id="buton-batchReject" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
                </li>
                {/if}
                {if $status neq '1'}
                <li>
                   <button name="status" value="1" id="buton-batchFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
                </li>
                {/if}
                {/acl}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_comments_config}" title="{t}Config comments module{/t}">
                        <img border="0" src="/themes/admin/images/template_manager/configure48x48.png" alt=""><br>
                        {t}Settings{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-right form-inline">
                <label>{t}Status:{/t}
                <select name="filter[status]" class="form-filters">
                    <option value="0" {if $status eq '0'}selected{/if}>{t}Pending{/t}</option>
                    <option value="1" {if $status eq '1'}selected{/if}>{t}Published{/t}</option>
                    <option value="2" {if $status eq '2'}selected{/if}>{t}Rejected{/t}</option>
                </select>
                </label>

                <label for="category">
                    {t}Category:{/t}
                    <select name="category" class="form-filters">
                        <option value="all" {if $category eq '0'}selected{/if}>{t}-- All --{/t} </option>
                        {section name=as loop=$allcategorys}
                             <option value="{$allcategorys[as]->pk_content_category}"
                                {if $allcategorys[as]->inmenu eq 0} class="unavailable" {/if}
                                {if isset($category) && ($category eq $allcategorys[as]->pk_content_category)}selected{/if}>
                                {$allcategorys[as]->title}</option>
                                {section name=su loop=$subcat[as]}
                                    {if $subcat[as][su]->internal_category eq 1}
                                    <option value="{$subcat[as][su]->pk_content_category}"
                                        {if $subcat[as][su]->inmenu eq 0} class="unavailable" {/if}
                                        {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">
                                        &nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                    {/if}
                                {/section}
                        {/section}
                    </select>
                </label>
                <div class="input-append">
                    <label>{t}Module:{/t}
                    <select name="filter[module]" class="form-filters">
                        <option value="0" {if $module eq '0'}selected{/if}>{t}-- All --{/t}</option>
                        {foreach from=$content_types key=i item=type}
                        <option value="{$i}" {if $module eq $i}selected{/if}>{$type}</option>
                        {/foreach}
                    </select>
                    </label>
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>

		<table class="table table-hover table-condensed">
			<thead>
    			{if count($comments) > 0}
                <tr>
                    <th style='width:15px'>
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t} - {t}Comment (50 chars){/t}</th>
                    <th style='width:6%;' class="left">{t}IP{/t}</th>
                    {if $category eq 'all'}
                        <th class="left">{t}Category{/t}</th>
                    {/if}
                    <th style='width:110px;' class="left">{t}Date{/t}</th>
                    <th style='width:20px;' class="center">{t}Votes{/t}</th>
                    <th style="width:10px;" class="center">{t}Published{/t}</th>
                    <th style='width:80px;' class="right">{t}Actions{/t}</th>
			   </tr>
               {else}
               <tr>
                    <th>
                        &nbsp;
                    </th>
               </tr>
			   {/if}
            </thead>

			<tbody>
            	{section name=c loop=$comments|default:array()}
				<tr style="cursor:pointer;" >
					<td >
						<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}"
                            name="selected_fld[]" value="{$comments[c]->id}">
					</td>
					<td>
						<a href="{url name=admin_comments_show id=$comments[c]->id}" title="{t 1=$articles[c]->title}Edit comment %1{/t}">
                            <strong>[{$comments[c]->title|strip_tags|clearslash|truncate:40:"..."}]</strong>
                            <span rel="tooltip" data-original-title="{$comments[c]->body|strip_tags|clearslash}">{$comments[c]->body|strip_tags|clearslash|truncate:50}</span>
                        </a>
                        <br>
                        <strong>{t}Author{/t}</strong>
                        {$comments[c]->author|strip_tags}
                        {if preg_match('/@proxymail\.facebook\.com$/i', $comments[c]->email)}
                            &lt;<span title="{$comments[c]->email}">{t}from facebook{/t}</span>&gt;
                        {else}
                            &lt;{$comments[c]->email}&gt;
                        {/if}
                        <br>
                        {assign var=type value=$contents[c]->content_type}
                        <strong>[{$content_types[$type]}]</strong>
                        {$contents[c]->title|strip_tags|clearslash}
					</td>
					<td class="left">
						{$comments[c]->ip}
					</td>
					{if $category eq 'all'}
					<td class="left">
						{$contents[c]->category_name}
                        {if $contents[c]->content_type==4}Opini&oacute;n{/if}
					</td>
					{/if}
					<td class="left">
						{$comments[c]->created}
					</td>
					<td class="center">
						{$votes[c]->value_pos} /  {$votes[c]->value_pos}
					</td>
					<td class="center">
                        {acl isAllowed="COMMENT_AVAILABLE"}
							{if $status eq 0}
								<a href="{url name=admin_comments_toggle_status id=$comments[c]->id status=1 category=$category page=$page return_status=$status}" title="Publicar">
									<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
								<a href="{url name=admin_comments_toggle_status id=$comments[c]->id status=2 category=$category page=$page return_status=$status}" title="Rechazar">
									<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
							{elseif $status eq 2}
								<a href="{url name=admin_comments_toggle_status id=$comments[c]->id status=1 category=$category page=$page return_status=$status}" title="Publicar">
									<img border="0" src="{$params.IMAGE_DIR}publish_r.png">
								</a>
							{else}
								<a href="{url name=admin_comments_toggle_status id=$comments[c]->id status=1 category=$category page=$page return_status=$status}" title="Rechazar">
									<img border="0" src="{$params.IMAGE_DIR}publish_g.png">
								</a>
							{/if}
                        {/acl}
					</td>
                    <td style="padding:1px; font-size:11px;" class="right">
                        <div class="btn-group">
                            {acl isAllowed="COMMENT_UPDATE"}
                                <a class="btn" href="{url name=admin_comments_show id=$comments[c]->id}" title="{t}Edit{/t}" >
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="COMMENT_DELETE"}
								<a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                   data-id="{$comments[c]->id}"
                                   data-title="{$comments[c]->title|capitalize}"
                                   data-url="{url name=admin_comments_delete id=$comments[c]->id}"
                                   href="{url name=admin_comments_delete id=$comments[c]->id}" >
								   <i class="icon-trash icon-white"></i>
                                </a>
                            {/acl}
						</div>
					</td>
				</tr>

				{sectionelse}
				<tr>
					<td class="empty" colspan=10>
						{t}There is no comments here.{/t}
					</td>
				</tr>
				{/section}
			</tbody>
			<tfoot>
				<tr>
					<td colspan="13">
                        <div class="pagination">
                            {$paginacion->links|default:""}
                        </div>
                    </td>
				</tr>
			</tfoot>

		</table>
    </div>

</form>
     <script>
        jQuery('#buton-batchReject').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_comments_batch_status category=$category page=$page}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_comments_batch_status category=$category page=$page}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });

        var comments_manager_urls = {
            batchDelete: '{url name=admin_comments_batch_delete category=$category page=$page}',
        }

    </script>
    {include file="comment/modals/_modalDelete.tpl"}
    {include file="comment/modals/_modalBatchDelete.tpl"}
    {include file="comment/modals/_modalAccept.tpl"}
{/block}
