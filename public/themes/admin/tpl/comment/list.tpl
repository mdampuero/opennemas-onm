{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script type="text/javascript">
    $('[rel=tooltip]').tooltip({ placement : 'bottom' });
</script>
{/block}

{block name="header-css" append}
<style type="text/css">
    .submitted-on {
        color: #777;
    }
</style>
{/block}

{block name="content"}
<form action="{url name=admin_comments}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
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
                       <img border="0" src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}"><br />{t}Delete{/t}
                   </a>
                </li>
                {/acl}
                {acl isAllowed="COMMENT_AVAILABLE"}
                {if $status neq '2'}
                <li>
                    <button name="status" value="rejected" id="buton-batchReject" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="{t}Unpublish{/t}" alt="{t}Unpublish{/t}" ><br />{t}Unpublish{/t}
                   </button>
                </li>
                {/if}
                {if $status neq '1'}
                <li>
                   <button name="status" value="accepted" id="buton-batchFrontpage" type="submit">
                       <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="{t}Publish{/t}" alt="{t}Publish{/t}" ><br />{t}Publish{/t}
                   </button>
                </li>
                {/if}
                {/acl}
                <li class="separator"></li>
                <li>
                    <a class="change" data-controls-modal="modal-comment-change" href="#" title="{t}Change comments module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}/template_manager/refresh48x48.png" alt="{t}Change system{/t}"><br>
                        {t}Change system{/t}
                    </a>
                </li>
                <li>
                    <a href="{url name=admin_comments_config}" title="{t}Config comments module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}/template_manager/configure48x48.png" alt="{t}Settings{/t}"><br>
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
                <div class="input-append">
                    <input name="filter_search" type="search" value="{$filter_search}" placeholder="{t}Search{/t}">
                    <select name="filter_status" class="form-filters">
                        {html_options options=$statuses selected=$filter_status}
                    </select>
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
                    <th>{t}Author{/t}</th>
                    <th>{t}Comment{/t}</th>
                    <th class="wrap">{t}In response to{/t}</th>
                    <th style='width:20px;' class="center">{t}Published{/t}</th>
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
            	{foreach $comments as $comment}
				<tr style="cursor:pointer;" >
					<td >
						<input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}"
                            name="selected_fld[]" value="{$comment->id}">
					</td>
					<td>
                        <strong>{$comment->author|strip_tags}</strong> <br>
                        {if $comment->author_email}
                        <a href="mailto:{$comment->author_email}">{$comment->author_email}</a>
                        {/if}
                        <br>
                        {$comment->author_ip}
					</td>
					<td class="left">
						<div class="submitted-on">{t}Submitted on:{/t} {date_format date=$comment->date}</div>
                        <p>
                            {$comment->body|strip_tags|clearslash|truncate:250:"..."}
                        </p>
					</td>
                    <td >
                        {$comment->content->title}
                    </td>
                    <td class="center">
                        {acl isAllowed="COMMENT_AVAILABLE"}
                            {if $comment->status eq Comment::STATUS_PENDING}
                                <a href="{url name=admin_comments_toggle_status id=$comment->id status=accepted category=$category page=$page return_status=$filter_status}" title="Publicar">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicar" /></a>
                                <a href="{url name=admin_comments_toggle_status id=$comment->id status=rejected category=$category page=$page return_status=$filter_status}" title="Rechazar">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Rechazar" /></a>
                            {elseif $comment->status eq Comment::STATUS_REJECTED}
                                <a href="{url name=admin_comments_toggle_status id=$comment->id status=accepted category=$category page=$page return_status=$filter_status}" title="Publicar">
                                    <img border="0" src="{$params.IMAGE_DIR}publish_g.png">
                                </a>
                            {else}
                                <a href="{url name=admin_comments_toggle_status id=$comment->id status=rejected category=$category page=$page return_status=$filter_status}" title="Rechazar">
                                    <img border="0" src="{$params.IMAGE_DIR}publish_r.png">
                                </a>
                            {/if}
                        {/acl}
                    </td>
					<td class="right">

                        <div class="btn-group">
                            {acl isAllowed="COMMENT_UPDATE"}
                                <a class="btn" href="{url name=admin_comment_show id=$comment->id}" title="{t}Edit{/t}" >
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="COMMENT_DELETE"}
								<a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                   data-id="{$comment->id}"
                                   data-title="{$comment->title|capitalize}"
                                   data-url="{url name=admin_comments_delete id=$comment->id}"
                                   href="{url name=admin_comments_delete id=$comment->id}" >
								   <i class="icon-trash icon-white"></i>
                                </a>
                            {/acl}
						</div>
					</td>
				</tr>

				{foreachelse}
				<tr>
					<td class="empty" colspan="6">
						{t}No comments matched your criteria.{/t}
					</td>
				</tr>
				{/foreach}
			</tbody>
			<tfoot>
				<tr>
					<td class="center" colspan="6">
                        <div class="pagination">{$pagination->links|default:""}</div>
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
    {include file="comment/modals/_modalChange.tpl"}
{/block}
