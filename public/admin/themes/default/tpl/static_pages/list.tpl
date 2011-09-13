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

        <table class="adminheading">
            <tr>
                <th align="right">
                    <label>{t}Title:{/t} <input type="text" name="filter[title]" for="submit" value="{$smarty.request.filter.title|default:""}" /></label>
                    <input type="submit" id="search" value="{t}Search{/t}">
                </th>
            </tr>
        </table><!--menu-heading-->

        <table class="listing-table">
            <thead>
                <tr>
                {if count($pages) > 0}
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th class="center" style="width:20px;">{t}Published{/t}</th>
                    <th class="center" style="width:20px;">{t}Actions{/t}</th>
                {else}
                    <th scope="col" colspan=4>&nbsp;</th>
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
                        <a href="?action=chg_status&id={$pages[k]->id}" class="available">
                            {if $pages[k]->available eq 1}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="{t}Unpublished{/t}" />
                            {/if}
                        </a>
                    </td>

                    <td class="center">
						<ul class="action-buttons">
							<li>
								<a href="{$smarty.server.PHP_SELF}?action=read&id={$pages[k]->id}" title="{t}Modify{/t}">
									<img src="{$params.IMAGE_DIR}edit.png" border="0" />
								</a>
							</li>
							<li>
								<a href="#" onClick="javascript:confirmar(this, '{$pages[k]->id}');" title="{t}Delete{/t}">
									<img src="{$params.IMAGE_DIR}trash.png" border="0" />
								</a>
							</li>
						</ul>
                    </td>
                </tr>
                {sectionelse}
                <tr>
                    <td align="center"><h2>{t}There is no static pages.{/t}</h2></td>
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

    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
    <script type="text/javascript" language="javascript">
        $('gridPages').select('a.available').each(function(item){
            new SwitcherFlag(item);
        });
    </script>
</div>
{/block}
