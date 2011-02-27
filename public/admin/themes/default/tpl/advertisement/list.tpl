{extends file="base/admin.tpl"}

{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}AdPosition.js"></script>


    <script defer="defer" type="text/javascript">
    // <![CDATA[
        Event.observe($('link_home'), 'mouseover', function(event) {
            $('menu_subcats').setOpacity(0);
                e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
            });
        Event.observe($('link_opinion'), 'mouseover', function(event) {
            $('menu_subcats').setOpacity(0);
                e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
            });
        Event.observe($('link_gallery'), 'mouseover', function(event) {
            $('menu_subcats').setOpacity(0);
                e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
            });
    // ]]>
    </script>
    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            $('page').value = 1;

            frm.submit();
        }
    </script>
{/block}


{block name="content"}
<div class="wrapper-content">

    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs} >

        <ul class="tabs2" style="margin-bottom: 28px;">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=0" id="link_home" {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}HOMEPAGE{/t}</font></a>
            </li>
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=4" id="link_opinion"  {if $category==4} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}OPINION{/t}</font></a>
            </li>
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=3" id="link_gallery"  {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}GALLERIES{/t}</font></a>
            </li>

            {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>
        <br style="clear: both;" />

        <div id="{$category}">

            <div id="menu-acciones-admin" class="clearfix">
                <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}::&nbsp;{$datos_cat[0]->title} {if $category eq 0}HOME{/if}</h2></div>
                <ul>
                    <li>
                        <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="{t}Delete{/t}" title="{t}Delete{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" title="{t}Delete{/t}" alt="{t}Delete{/t}"><br />{t}Delete{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                            <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                        </a>
                    </li>
                    <li>
                        <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                            <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />{t}Publish{/t}
                        </a>
                    </li>
                    <li>
                        <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                            <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
                        </button>
                    </li>

                    <li>
                        <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=new&category={$_REQUEST['category']}&page={$_GET['page']}"
                           class="admin_add" accesskey="N" tabindex="1">
                            <img border="0" src="{$params.IMAGE_DIR}advertisement.png" title="{t}New{/t}" alt="{t}New{/t}"><br />{t}New{/t}
                        </a>
                    </li>
                </ul>
            </div>



            <table class="adminheading">
                <tr>
                    <th nowrap="nowrap" align="right">
                        <label for="filter[type_advertisement]">{t}Banner type:{/t}</label>
                        <select name="filter[type_advertisement]" onchange="submitFilters(this.form);">
                            {html_options options=$filter_options.type_advertisement selected=$smarty.request.filter.type_advertisement}
                        </select>
                        &nbsp;&nbsp;&nbsp;
                        <label>{t}Status:{/t}</label>
                        <select name="filter[available]" onchange="submitFilters(this.form);">
                            {html_options options=$filter_options.available selected=$smarty.request.filter.available}
                        </select>
                         &nbsp;&nbsp;&nbsp;
                        <label>{t}Type:{/t}</label>
                        <select name="filter[type]" onchange="submitFilters(this.form);">
                            {html_options options=$filter_options.type selected=$smarty.request.filter.type}
                        </select>
                        <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:"1"}" />
                    </th>
                </tr>
            </table>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th></th>
                        <th class="title">{t}Type{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th align="center">{t}Permanence{/t}</th>
                        <th align="center">{t}Clicks{/t}</th>
                        <th align="center">{t}Viewed{/t}</th>
                        <th align="center">{t}Type{/t}</th>
                        <th align="center">{t}Published{/t}</th>
                        <th align="center">{t}Edit{/t}</th>
                        <th align="center">{t}Delete{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    {section name=c loop=$advertisements}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="text-align:center;font-size: 11px;width:5%;">
                            <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
                                value="{$advertisements[c]->pk_advertisement}" />
                        </td>
                        <td style="font-size: 11px;">
                            <label for="title">
                                {assign var="type_advertisement" value=$advertisements[c]->type_advertisement}
                                {$map.$type_advertisement}
                            </label>
                        </td>
                        <td style="font-size: 11px;">
                            {$advertisements[c]->title|clearslash}
                        </td>

                        <td style="text-align:center;font-size: 11px;width:80px;" align="center">
                            {if $advertisements[c]->type_medida == 'NULL'} {t}Undefined{/t} {/if}
                            {if $advertisements[c]->type_medida == 'CLIC'} {t}Clicks:{/t} {$advertisements[c]->num_clic} {/if}
                            {if $advertisements[c]->type_medida == 'VIEW'} {t}Viewed:{/t} {$advertisements[c]->num_view} {/if}
                            {if $advertisements[c]->type_medida == 'DATE'}
                                {t}Date:{/t} {$advertisements[c]->starttime|date_format:"%d:%m:%Y"}-{$advertisements[c]->endtime|date_format:"%d:%m:%Y"}
                            {/if}
                        </td>

                        <td style="text-align:center;font-size: 11px;width:105px;" align="right">
                            {$advertisements[c]->num_clic_count}
                        </td>
                        <td style="text-align:center;font-size: 11px;width:40px;" align="right">
                             {$advertisements[c]->views}
                        </td>
                        <td style="text-align:center;font-size: 11px;width:70px;" align="center">
                            {if $advertisements[c]->with_script == 1}
                                <img src="{$params.IMAGE_DIR}iconos/script_code_red.png" border="0"
                                     alt="Javascript" title="Javascript" />
                            {else}
                                <img src="{$params.IMAGE_DIR}iconos/picture.png" border="0" alt="{t}Media{/t}"
                                     title="{t}Media element (flash, image, gif){/t}" />
                            {/if}
                        </td>
                        <td style="text-align:center;width:70px;" align="center">
                            {if $advertisements[c]->available == 1}
                                <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;category={$category}&amp;status=0&amp;&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                    title={t}"Published"{/t}>
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                            {else}
                                <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                    title={t}"Unresolved"{/t}>
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Pending{/t}" /></a>
                            {/if}
                        </td>

                        <td style="text-align:center;width:70px;" align="center">
                            <a href="{$smarty.server.PHP_SELF}?action=read&id={$advertisements[c]->id}" title="{t}Edit{/t}">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </td>

                        <td style="text-align:center;width:70px;" align="center">
                            <a href="#" onClick="javascript:confirmar(this, '{$advertisements[c]->id}');" title="{t}Delete{/t}">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                        </td>

                    </tr>
                    {sectionelse}
                    <tr>
                        <td align="center" colspan="10">
                            <h2>{t}There is no advertisement stored in this section{/t}</h2>
                        </td>
                    </tr>
                    {/section}
                </tbody>

                <tfoot >
                    <tr class="pagination">
                        <td colspan="10">
                            {$paginacion->links}
                        </td>
                    </tr>
                </tfoot>

            </table>

        </div><!--fin content-wrapper-->

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />

    </form>
</div>
{/block}
