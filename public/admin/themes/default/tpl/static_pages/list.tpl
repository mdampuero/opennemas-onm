{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar">
	<div class="wrapper-content">
		<div class="title"><h2>{t}Static Pages Manager{/t} :: {t}Listing static pages{/t}</h2></div>
		<ul class="old-button">
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=new" title="Nueva Página">
					<img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}New static page{/t}" alt="" /><br />{t}New page{/t}
				</a>
			</li>
		</ul>
	</div>
</div>
<div class="wrapper-content">

	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

        <div class="table-info clearfix">
            <div>
                <div class="right form-inline">
                    <label>
                        {t}Title:{/t}
                        <input type="search" class="search-query" name="filter[title]" for="submit" value="{$smarty.request.filter.title|default:""}" />
                    </label>
                    <button type="submit" id="search" class="btn">{t}Search{/t}</button>
                </div>
            </div>
        </div>

        <table class="listing-table table table-striped">
            <thead>
                <tr>
                {if count($pages) > 0}
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:20px;">{t}Published{/t}</th>
                    <th class="center" style="width:100px;">{t}Actions{/t}</th>
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
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html" target="_blank" title="{t}Open in a new window{/t}">
                            {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html
                        </a>
                    </td>

                    <td class="center">
                        {$pages[k]->views}
                    </td>

                    <td class="center">
                        {acl isAllowed="STATIC_AVAILABLE"}
                        <a href="?action=chg_status&id={$pages[k]->id}" class="available">
                        {/acl}
                            {if $pages[k]->available eq 1}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="{t}Unpublished{/t}" />
                            {/if}
                        </a>
                    </td>

                    <td class="center">
                        {acl isAllowed="STATIC_UPDATE"}
							<a class="btn btn-mini" href="{$smarty.server.PHP_SELF}?action=read&id={$pages[k]->id}" title="{t}Modify{/t}">
								{t}Edit{/t}
							</a>
                        {/acl}
                        {acl isAllowed="STATIC_DELETE"}
							<a class="del btn btn-mini btn-danger" data-controls-modal="modal-from-dom"
                               data-id="{$pages[k]->id}" title="{t}Delete{/t}"
                               data-title="{$pages[k]->title|capitalize}" href="#" >
                                {t}Delete{/t}
							</a>
                        {/acl}
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td class="empty">{t}There is no static pages.{/t}</td>
                </tr>
                {/section}
            </tbody><!--menu-adminlist-->
            {if count($pages) > 0}
            <tfoot>
                <tr class="pagination">
                    <td colspan=5>{$pager->links}</td>
                </tr>
            </tfoot>
            {/if}
        </table>

        <input type="hidden" id="action" name="action" value="list" />
        <input type="hidden" id="id" name="id" value="" />
    </form>

    {script_tag src="/switcher_flag.js" language="javascript"}
    <script type="text/javascript" language="javascript">
        $('gridPages').select('a.available').each(function(item){
            new SwitcherFlag(item);
        });
    </script>
</div>
     {include file="static_pages/modals/_modalDelete.tpl"}
{/block}
