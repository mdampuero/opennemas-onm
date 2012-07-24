{extends file="base/admin.tpl"}


{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
	<div class="top-action-bar">
		<div class="wrapper-content">
            <div class="title"><h2>{t}Opinion Manager :: Author list{/t}</div>
            <ul class="old-button">
                 {acl isAllowed="AUTHOR_CREATE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-author-batchDelete" href="#" title="{t}Delete{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="AUTHOR_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new&amp;page=0" class="admin_add"  accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="{t}New author{/t}" alt="{t}New author{/t}"><br />{t}New author{/t}
                    </a>
                </li>
                {/acl}
                <li class="separator"></li>
                {acl isAllowed="OPINION_ADMIN"}
                <li >
                    <a href="opinion.php?action=list&amp;desde=author" class="admin_add" name="submit_mult" value="Listado Opiniones" title="Listado Opiniones">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Listado Opiniones" alt="Listado Opiniones"><br />{t escape="off"}Go back{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
	</div>

    <div class="wrapper-content">

        {render_messages}

        <table class="listing-table">
            <thead>
                <tr>
                    <th style="width:15px">
                        <input type="checkbox" id="toggleallcheckbox" />
                    </th>
                    <th class="title">{t}Author name{/t}</th>
                    <th class="title">{t}Condition{/t}</th>
                    <th>{t}Blog name{/t}</th>
                    <th>{t}Blog url{/t}</th>
                    <th class="title" style="text-align:center">{t}Photos (#){/t}</th>
                    <th class="right" style="width:100px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            {foreach from=$authors item=author name=c}
                <tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
                     <td style="text-align:center;">
                        <input type="checkbox" class="minput" id="selected_{$smarty.foreach.c.iteration}" name="selected_fld[]"
                            value="{$author->pk_author}" />
                    </td>
                    <td>
                        {$author->name}&nbsp;&nbsp;{*if $author->fk_user != 0}(usuario){/if*}
                    </td>
                    <td>
                        {$author->condition}
                    </td>
                    <td>
                        {$author->politics}
                    </td>
                    <td>
                        {$author->gender}
                    </td>
                    <td style="text-align:center">
                        {$author->num_photos}
                    </td>
                    <td class="right">
						<div class="btn-group">
                            {acl isAllowed="AUTHOR_UPDATE"}
								<a class="btn" href="{url name=admin_opinion_author_show id=$author->pk_author}" title="Modificar">
									<i class="icon-pencil"></i>
								</a>
                            {/acl}
                            {acl isAllowed="AUTHOR_DELETE"}
								<a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                   data-id="{url name=admin_opinion_authors_delete id=$author->pk_author}"
                                   data-title="{$author->name|capitalize}"
                                   href="{url name=admin_opinion_authors_delete id=$author->pk_author}">
                                   <i class="icon-trash icon-white"></i>
								</a>
                            {/acl}
						</div>
                    </td>
                </tr>

            {foreachelse}
                <tr>
                    <td align="center" colspan="7"><b>{t}There is no available authors{/t}</b></td>
                </tr>
            {/foreach}
            <tfoot>
                <tr class="pagination">
                    <td colspan="7" align="center">
                        {$pagination->links}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div><!--fin wrapper-content-->
</form>
    {include file="opinion/authors/modals/_modalDelete.tpl"}
    {include file="opinion/authors/modals/_modalBatchDelete.tpl"}
    {include file="opinion/authors/modals/_modalAccept.tpl"}
{/block}
