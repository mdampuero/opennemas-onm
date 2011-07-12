{extends file="base/admin.tpl"}

{block name="content"}
<div class="wrapper-content">

	<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

        <div id="menu-acciones-admin">
            <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{t}Static Pages Manager{/t} :: {t}Listing static pages{/t}</h2></div>
		<ul>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" title="Nueva Página">
                        <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}New static page{/t}" alt="" /><br />{t}New page{/t}
                    </a>
                </li>
            </ul>
        </div><!--menu-acciones-admin-->
        <br>

        <table class="adminheading">
            <tr>
                <th nowrap="nowrap" align="right">
                    <label>Título: <input type="text" name="filter[title]" value="{$smarty.request.filter.title|default:""}" /></label>
                    <input type="submit" value="{t}Search{/t}">
                </th>
            </tr>
        </table><!--menu-heading-->

        <table class="adminlist">
            {if count($pages) > 0}
            <thead>
                <tr>
                    <th>{t}Title{/t}</th>
                    <th>{t}URL{/t}</th>
                    <th>{t}Visits{/t}</th>
                    <th>{t}Published{/t}</th>
                    <th>{t}Actions{/t}</th>
                </tr>
            </thead>
            {/if}
            <tbody id="gridPages">
                {section name=k loop=$pages}
                <tr bgcolor="{cycle values="#eeeeee,#ffffff"}">
                    <td>
                        {$pages[k]->title}
                    </td>
                    <td>&raquo;
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html" target="_blank" title="{t}Open in a new window{/t}">
                            {$smarty.const.SITE_URL}{$smarty.const.STATIC_PAGE_PATH}{$pages[k]->slug}.html</a>
                    </td>

                    <td width="44" align="right">
                        {$pages[k]->views}
                        &nbsp;&nbsp;
                    </td>

                    <td width="44" align="center">
                        <a href="?action=chg_status&id={$pages[k]->id}" class="available">
                            {if $pages[k]->available eq 1}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" title="{t}Unpublished{/t}" />
                            {/if}
                        </a>
                    </td>

                    <td width="64" align="center">
                        <a href="{$smarty.server.PHP_SELF}?action=read&id={$pages[k]->id}" title="{t}Modify{/t}">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        &nbsp;&nbsp;
                        <a href="#" onClick="javascript:confirmar(this, '{$pages[k]->id}');" title="{t}Delete{/t}">
                            <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
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
    </form>

    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}switcher_flag.js"></script>
    <script type="text/javascript" language="javascript">
        $('gridPages').select('a.available').each(function(item){
            new SwitcherFlag(item);
        });
    </script>
</div>
{/block}
