{extends file="base/admin.tpl"}

{block name="footer-js"}
<script>
jQuery(function($){
    $('#batch-delete').on('click', function(e, ui) {
        var form = $('#trashform');
        form.attr('action', '{url name=admin_trash_batchdelete}');
    })
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
				<a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mremove', 6);"  onmouseover="return escape('<u>E</u>liminar todos');">
					<img border="0" src="{$params.IMAGE_DIR}trash.png" alt="Eliminar todos"><br />{t}Delete all{/t}
				</a>
			</li>
			<li>
                <button type="submit" id="batch-delete" title="{t}Deletes the selected elements{/t}">
                    <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar"><br />{t}Delete{/t}
                </button>
			</li>
            <li class="separator"></li>
			<li>
				<a href="#" class="admin_add" onClick="javascript:enviar3(this, '_self', 'm_no_in_litter', 0);" title="Recuperar">
				    <img border="0" src="{$params.IMAGE_DIR}trash_no.png" title="Recuperar" alt="Recuperar"><br />{t}Restore{/t}
				</a>
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

        <table class="listing-table">

            <thead>
               <tr>
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th class='left'>{t}Title{/t}</th>
                    <th style="width:40px">{t}Section{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:110px;">{t}Date{/t}</th>
                    <th class="center" style="width:120px;">{t}Actions{/t}</th>
               </tr>
            </thead>

            <tbody>
                {section name=c loop=$litterelems}
                <tr>
                    <td >
                        <input type="checkbox" name="selected[]" value="{$litterelems[c]->id}">
                    </td>
                    <td>
                        {$litterelems[c]->title|clearslash}
                    </td>
                    <td class="center">{$litterelems[c]->category_title}</td>
                    <td class="center">{$litterelems[c]->views}</td>
                    <td class="center">{$litterelems[c]->created}</td>
                    <td class="right">
                        <div class="btn-group">
                            <a class="btn" href="{$smarty.server.PHP_SELF}?id={$litterelems[c]->id}&amp;action=no_in_litter&amp;mytype={$mytype}&amp;page={$paginacion->_currentPage}" title="Recuperar">
                                <i class="icon-retweet"></i> {t}Restore{/t}
                            </a>
                            <a class="btn btn-danger" href="{url name=admin_trash_delete id=$litterelems[c]->id mytype=$mytype page=$paginacion->_currentPage}" title="{t}Delete this content{/t}">
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
                <tr class="pagination">
                    <td colspan="6">
                        {$paginacion->links}&nbsp;
                    </td>
                </tr>
            </tfoot>

        </table>

    </div>
</form>
{/block}
