{extends file="base/admin.tpl"}

{block name="footer-js" append}
<script>
jQuery(function($){
    $('#batch-delete').click(function(e) {
        //Sets up the modal
        jQuery("#modal-delete-contents").modal('show');
        e.preventDefault();
    });
    $('#batch-restore').click(function(e) {
        //Sets up the modal
        jQuery("#modal-restore-contents").modal('show');
        e.preventDefault();
    });
});
</script>
{/block}

{block name="admin_menu"}
<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Trash{/t}</h2></div>
        <ul class="old-button">
            {acl isAllowed="TRASH_ADMIN"}
			<li>
                <button type="submit" id="batch-delete" title="{t}Deletes the selected elements{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                </button>
			</li>
			<li>
				<button type="submit" id="batch-restore" title="{t}Restore{/t}">
				    <img border="0" src="{$params.IMAGE_DIR}trash_no.png" title="Recuperar" alt="Recuperar"><br />{t}Restore{/t}
				</button>
			</li>
            {/acl}
		</ul>
	</div>
</div>
{/block}

{block name="content"}
<form action="{url name=admin_trash}" method="post" id="trashform">
{block name="admin_menu"}{/block}
	<div class="wrapper-content">
        {render_messages}

        {include file="trash/partials/_pills.tpl"}

        <table class="table table-hover table-condensed">

            <thead>
               <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th class='left'>{t}Title{/t}</th>
                    <th style="width:40px">{t}Section{/t}</th>
                    <th class="left" style="width:110px;">{t}Date{/t}</th>
                    <th class="center" style="width:20px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="nowrap center" style="width:40px;">{t}Actions{/t}</th>
               </tr>
            </thead>

            <tbody>
                {section name=c loop=$contents}
                <tr>
                    <td >
                        <input type="checkbox" name="selected[]" value="{$contents[c]->id}">
                    </td>
                    <td>{$contents[c]->title|clearslash}</td>
                    <td class="left">{$contents[c]->category_title}</td>
                    <td class="center">{$contents[c]->created}</td>
                    <td class="center">{$contents[c]->views}</td>
                    <td class="nowrap right">
                        <div class="btn-group">
                            <a class="btn" href="{url name=admin_trash_restore id=$contents[c]->id mytype=$mytype page=$paginacion->_currentPage}" title="Recuperar">
                                <i class="icon-retweet"></i> {t}Restore{/t}
                            </a>
                            <a class="btn btn-danger" href="{url name=admin_trash_delete id=$contents[c]->id mytype=$mytype page=$paginacion->_currentPage}" title="{t}Delete this content{/t}">
                                <i class="icon-trash icon-white"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr >
                    <td class="empty"colspan=6>
                        {t}There is no elements in the trash{/t}
                    </td>
                </tr>
                {/section}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>
</form>
{include file="trash/modals/_modalDelete.tpl"}
{include file="trash/modals/_modalRestore.tpl"}
{/block}
