{extends file="base/admin.tpl"}

{block name="content"}
<form action="{url name=admin_opinion_authors}" method="GET" name="formulario" id="formulario">
	<div class="top-action-bar clearfix">
		<div class="wrapper-content">
            <div class="title"><h2>{t}Opinion authors{/t}</div>
            <ul class="old-button">
                 {acl isAllowed="AUTHOR_DELETE"}
                <li>
                    <a class="delChecked" data-controls-modal="modal-author-batchDelete" href="#" title="{t}Delete{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="AUTHOR_CREATE"}
                <li>
                    <a href="{url name=admin_opinion_author_create}" class="admin_add"  accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}authors_add.png" title="{t}New author{/t}" alt="{t}New author{/t}"><br />{t}New author{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
	</div>

    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-right form-inline">
                <div class="input-append">
                    <input type="text" id="username" name="name" value="{$name|default:""}" placeholder="{t}Filter by name{/t}"  />
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:15px">
                        <input type="checkbox" class="toggleallcheckbox" />
                    </th>
                    <th class="title">{t}Author name{/t}</th>
                    <th class="title">{t}Condition{/t}</th>
                    <th class="title" style="text-align:center">{t}Photos (#){/t}</th>
                    <th class="right" style="width:100px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$authors item=author name=c}
                <tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
                     <td style="text-align:center;">
                        <input type="checkbox" class="minput" id="selected_{$smarty.foreach.c.iteration}" name="selected_fld[]"
                            value="{$author->pk_author}" />
                    </td>
                    <td>
                        {$author->name}
                    </td>
                    <td>
                        {$author->condition}
                    </td>
                    <td style="text-align:center">
                        {$author->numphotos}
                    </td>
                    <td class="right nowrap">
                        <div class="btn-group">
                            {acl isAllowed="AUTHOR_UPDATE"}
                                <a class="btn" href="{url name=admin_opinion_author_show id=$author->pk_author}" title="Modificar">
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="AUTHOR_DELETE"}
                                <a class="del btn btn-danger"
                                   data-url="{url name=admin_opinion_author_delete id=$author->pk_author}"
                                   data-title="{$author->name|capitalize}"
                                   href="{url name=admin_opinion_author_delete id=$author->pk_author}">
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
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
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
