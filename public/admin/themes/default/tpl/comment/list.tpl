{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            $('page').value = 1;

            frm.submit();
        }
    </script>
{/block}

{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar" class="clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>
                    {$titulo_barra} ::
                    {if $category eq 'home' ||  $category eq 'todos'} {$category|upper} {else} {$datos_cat[0]->title} {/if}
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
               <li>
                    <button value="batchReject" name="buton-batchReject" id="buton-batchReject" type="submit">
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
        </div>
    </div>
    <div class="wrapper-content">

		<div class="clearfix">

            <ul class="pills">
                <li>
					<a id="pending-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&module={$smarty.get.module}&comment_status=0" {if $comment_status==0}class="active"{/if} >{t}Pending{/t}</a>
                </li>
                <li>
					<a id="published-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&module={$smarty.get.module}&comment_status=1" {if $comment_status==1}class="active"{/if}>{t}Published{/t}</a>
                </li>
                <li>
					<a id="rejected-tab" href="{$smarty.server.SCRIPT_NAME}?action=list&category={$category}&module={$smarty.get.module}&comment_status=2" {if $comment_status==2}class="active"{/if}>{t}Rejected{/t}</a>
                </li>
            </ul>

        </div>

        <div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    <label for="category">
                        {t}Category name:{/t}
                        <select name="category" onchange="submitFilters(this.form);">
                            <option value="todos" {if $category eq '0'}selected{/if}> {t}All{/t} </option>
                            {section name=as loop=$allcategorys}
                                 <option value="{$allcategorys[as]->pk_content_category}" {if isset($category) && ($category eq $allcategorys[as]->pk_content_category)}selected{/if}>{$allcategorys[as]->title}</option>
                                 {section name=su loop=$subcat[as]}
                                        {if $subcat[as][su]->internal_category eq 1}
                                            <option value="{$subcat[as][su]->pk_content_category}"
                                            {if $category eq $subcat[as][su]->pk_content_category || $article->category eq $subcat[as][su]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;|_&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                        {/if}
                                    {/section}
                            {/section}
                        </select>
                    </label>

                    <label>{t}Module:{/t}
                    <select name="module" onchange="submitFilters(this.form);">
                        <option value="0" {if $module eq '0'}selected{/if}> {t}All{/t} </option>
                        {foreach from=$content_types key=i item=type}
                        <option value="{$i}" {if $smarty.get.module eq $i}selected{/if}>{$type}</option>
                        {/foreach}
                    </select>
                    </label>
                    <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:"1"}" />
                </div>
            </div>
        </div>

		<div id="{$category}">

			<table class="listing-table">
				<thead>
        			{if count($comments) > 0}
                    <tr>
                        <th style='width:15px'>
                            <input type="checkbox" id="toggleallcheckbox">
                        </th>
                        <th>{t}Title{/t} - {t}Comment (50 chars){/t}</th>
                        <th style='width:200px;'>{t}Commented on{/t}</th>
                        <th  style='width:6%;' class="center">{t}IP{/t}</th>
                        {if $category eq 'todos' || $category eq 'home'}
                            <th class="center" style="width:5%;">{t}Category{/t}</th>
                        {/if}
                        <th  style='width:110px;' class="center">{t}Date{/t}</th>
                        <th style='width:20px;' class="center">{t}Votes{/t}</th>
                        <th style='width:90px;' class="center">{t}Actions{/t}</th>
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
					<tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
						<td >
							<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$comments[c]->id}"  style="cursor:pointer;" >
						</td>
						<td onmouseout="UnTip()" onmouseover="Tip('{$comments[c]->body|nl2br|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, true, ABOVE, true, WIDTH, 600)" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();">
							<a href="{$smarty.server.PHP_SELF}?action=read&id={$comments[c]->pk_comment}" title="{t 1=$articles[c]->title}Edit article %1{/t}">
                                {$comments[c]->author|strip_tags}
                                {if preg_match('/@proxymail\.facebook\.com$/i', $comments[c]->email)}
                                    &lt;<span title="{$comments[c]->email}">{t}from facebook{/t}</span>&gt;
                                {else}
                                    &lt;{$comments[c]->email}&gt;
                                {/if}<br>
								<strong>[{$comments[c]->title|strip_tags|clearslash|truncate:40:"..."}]</strong>
                                {$comments[c]->body|strip_tags|clearslash|truncate:50}
                            </a>
						</td>
						 {assign var=type value=$articles[c]->content_type}
						<td >
							<strong>[{$content_types[$type]}]</strong>
							{$articles[c]->title|strip_tags|clearslash}
						</td>
						<td class="center">
							{$comments[c]->ip}
						</td>
						{if $category eq 'todos' || $category eq 'home'}
						<td class="center">
							{$articles[c]->category_name} {if $articles[c]->content_type==4}Opini&oacute;n{/if}
						</td>
						{/if}
						<td class="center">
							{$comments[c]->created}
						</td>
						<td class="center">
							{$votes[c]->value_pos} /  {$votes[c]->value_pos}
						</td>
						<td class="right">
							<ul class="action-buttons">
                                 {acl isAllowed="COMMENT_AVAILABLE"}
								<li>
									{if $category eq 'todos' || $comments[c]->content_status eq 0}
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Publicar">
												<img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Rechazar">
												<img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
									{elseif $comments[c]->content_status eq 2}
										<a href="?id={$comments[c]->id}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Publicar">
											<img border="0" src="{$params.IMAGE_DIR}publish_g.png">
										</a>
									{else}
										<a class="publishing" href="?id={$comments[c]->id}&amp;action=change_status&amp;status=2&amp;category={$category}&amp;comment_status={$comment_status}&amp;page={$paginacion->_currentPage|default:0}" title="Rechazar">
											<img border="0" src="{$params.IMAGE_DIR}publish_g.png">
										</a>
									{/if}
								</li>
                                {/acl}
                                 {acl isAllowed="COMMENT_UPDATE"}
								<li>
									<a href="{$smarty.server.PHP_SELF}?action=read&id={$comments[c]->id}" title="Modificar">
										<img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
								</li>
                                {/acl}
                                {acl isAllowed="COMMENT_DELETE"}
								<li>
									<a class="del" data-controls-modal="modal-from-dom"
                               data-id="{$comments[c]->id}"
                               data-title="{$comments[c]->title|capitalize}"  href="#" >
										<img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
								</li>
                                {/acl}
							</ul>
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
					<tr class="pagination">
						<td colspan="13">
                            {$paginacion->links|default:""}&nbsp;
                        </td>
					</tr>
				</tfoot>

			</table>
		</div>
    </div>

    <input type="hidden" name="comment_status" id="comment_status" value="{$comment_status}" />
    <input type="hidden" name="category" id="category" value="{$category}" />
    <input type="hidden" id="status" name="status" value="" />
	<input type="hidden" id="action" name="action" value="" />
	<input type="hidden" name="id" id="id" value="{$id|default:""}" />

</form>
     <script>
        jQuery('#buton-batchReject').on('click', function(){
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "2");
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
    {include file="comment/modals/_modalDelete.tpl"}
    {include file="comment/modals/_modalBatchDelete.tpl"}
    {include file="comment/modals/_modalAccept.tpl"}
{/block}
