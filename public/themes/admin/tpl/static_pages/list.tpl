{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Static pages{/t}</h2></div>
		<ul class="old-button">
			<li>
				<a href="{url name=admin_staticpages_create}" title="{t}Create new page{/t}">
					<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}New static page{/t}" alt="" /><br />{t}New page{/t}
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="wrapper-content">

    {render_messages}

	<form action="{url name=admin_staticpages}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

        <div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    <div class="input-append">
                        <input type="search" name="filter[title]" for="submit" value="{$smarty.request.filter.title|default:""}" placeholder="{t}Filter by title{/t}"/>
                        <button type="submit" id="search" class="btn"><i class="icon-search"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                {if count($pages) > 0}
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:20px;">{t}Published{/t}</th>
                    <th class="center" style="width:80px;">{t}Actions{/t}</th>
                {else}
                {/if}
                </tr>
            </thead>
            <tbody>
                {section name=k loop=$pages}
                <tr>

                    <td>
                        {$pages[k]->title}
                    </td>

                    <td>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/{$pages[k]->slug}/" target="_blank" title="{t}Open in a new window{/t}">
                            {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}/{$pages[k]->slug}/
                        </a>
                    </td>

                    <td class="center">
                        {$pages[k]->views}
                    </td>

                    <td class="center">
                        {acl isAllowed="STATIC_AVAILABLE"}
                        {if $pages[k]->available eq 1}
                            <a href="{url name=admin_staticpages_toggle_available id=$pages[k]->id status=0 page=$page}" class="unavailable">
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="{t}Published{/t}" />
                            </a>
                        {else}
                            <a href="{url name=admin_staticpages_toggle_available id=$pages[k]->id status=1 page=$page}" class="available">
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="{t}Unpublished{/t}" />
                            </a>
                        {/if}
                        {/acl}
                    </td>

                    <td class="right nowrap">
                        <div class="btn-group">
                        {acl isAllowed="STATIC_UPDATE"}
                            <a class="btn" href="{url name=admin_staticpage_show id=$pages[k]->id}" title="{t}Modify{/t}">
                                <i class="icon-pencil"></i>{t}Edit{/t}
                            </a>
                        {/acl}
                        {acl isAllowed="STATIC_DELETE"}
                            <a class="del btn btn-danger" data-controls-modal="modal-from-dom"
                               data-url="{url name=admin_staticpages_delete id=$pages[k]->id}"
                               title="{t}Delete{/t}"
                               data-title="{$pages[k]->title|capitalize}" href="{url name=admin_staticpages_delete id=$pages[k]->id}" >
                                <i class="icon-trash icon-white"></i>
                            </a>
                        {/acl}

                        </div>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty">{t}There is no static pages.{/t}</td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan=5 class="center">
                        <div class="pagination">{$pager->links}</div>
                    </td>
                </tr>
            </tfoot>
        </table>

    </form>

</div>
     {include file="static_pages/modals/_modalDelete.tpl"}
{/block}
