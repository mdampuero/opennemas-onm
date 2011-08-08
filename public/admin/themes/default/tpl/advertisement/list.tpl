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
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra}::&nbsp; {if $category eq 0}HOME{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="{t}Delete{/t}" title="{t}Delete{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}"><br />{t}Delete{/t}
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
                    <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=new&category={$smarty.request.category}&page={$smarty.get.page}"
                       class="admin_add" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="{t}New{/t}" alt="{t}New{/t}"><br />{t}New{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">


        <ul class="tabs2">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=0" id="link_home" {if $category==0} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>{t}HOMEPAGE{/t}</font></a>
            </li>

            {include file="menu_categorys.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        <div id="{$category}">

            <table class="adminheading">
                <tr>
                    <th nowrap="nowrap" align="right">
                        <label for="filter[type_advertisement]">{t}Banner type:{/t}</label>
                        <select name="filter[type_advertisement]" onchange="submitFilters(this.form);">
                            {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type_advertisement)}
                                {assign var=filterType value=""}
                            {else}
                                {assign var=filterType value=$smarty.request.filter.type_advertisement|default:""}
                            {/if}
                            {html_options options=$filter_options.type_advertisement selected=$filterType}
                        </select>
                        &nbsp;&nbsp;&nbsp;
                        <label>{t}Status:{/t}</label>
                        <select name="filter[available]" onchange="submitFilters(this.form);">
                            {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type_advertisement)}
                                {assign var=filterAvailable value=""}
                            {else}
                                {assign var=filterAvailable value=$smarty.request.filter.available|default:""}
                            {/if}
                            {html_options options=$filter_options.available selected=$filterAvailable}
                        </select>
                         &nbsp;&nbsp;&nbsp;
                        <label>{t}Type:{/t}</label>
                        <select name="filter[type]" onchange="submitFilters(this.form);">
                            {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type)}
                                {assign var=filterType value=""}
                            {else}
                                {assign var=filterType value=$smarty.request.filter.type|default:""}
                            {/if}
                            {html_options options=$filter_options.type selected=$filterType}
                        </select>
                        <input type="hidden" id="page" name="page" value="{$smarty.request.page|default:"1"}" />
                    </th>
                </tr>
            </table>

            <table class="adminlist">
                <thead>
                    <tr>
                        <th  style="width:10px"></th>
                        <th class="title"  style="width:250px">{t}Type{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th align="center" style="width:30px">{t}Permanence{/t}</th>
                        <th align="center" style="width:40px">{t}Clicks{/t}</th>
                        <th align="center" style="width:40px">{t}Views{/t}</th>
                        <th align="center" style="width:70px">{t}Actions{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    {section name=c loop=$advertisements|default:""}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="text-align:center;">
                            <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
                                value="{$advertisements[c]->pk_advertisement}" />
                        </td>
                        <td style="">
                            <label for="title">
                                {if $advertisements[c]->with_script == 1}
                                    <img src="{$params.IMAGE_DIR}iconos/script_code_red.png" border="0"
                                         alt="Javascript" title="Javascript" />
                                {else}
                                    <img src="{$params.IMAGE_DIR}iconos/picture.png" border="0" alt="{t}Media{/t}"
                                         title="{t}Media element (flash, image, gif){/t}" />
                                {/if}
                                {assign var="type_advertisement" value=$advertisements[c]->type_advertisement}
                                {$map.$type_advertisement}
                            </label>
                        </td>
                        <td style="">
                            {$advertisements[c]->title|clearslash}
                        </td>

                        <td style="text-align:center;" align="center">
                            {if $advertisements[c]->type_medida == 'NULL'} {t}Undefined{/t} {/if}
                            {if $advertisements[c]->type_medida == 'CLIC'} {t}Clicks:{/t} {$advertisements[c]->num_clic} {/if}
                            {if $advertisements[c]->type_medida == 'VIEW'} {t}Viewed:{/t} {$advertisements[c]->num_view} {/if}
                            {if $advertisements[c]->type_medida == 'DATE'}
                                {t}Date:{/t} {$advertisements[c]->starttime|date_format:"%d:%m:%Y"}-{$advertisements[c]->endtime|date_format:"%d:%m:%Y"}
                            {/if}
                        </td>

                        <td style="text-align:center;" align="right">
                            {$advertisements[c]->num_clic_count}
                        </td>
                        <td style="text-align:center;" align="right">
                             {$advertisements[c]->views}
                        </td>
                        <td style="text-align:center;" align="center">
                            <ul class="action-buttons">
                                <li>
                                    {if $advertisements[c]->available == 1}
                                        <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;category={$category}&amp;status=0&amp;&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                            title={t}"Published"{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                                    {else}
                                        <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                            title={t}"Unresolved"{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}Pending{/t}" /></a>
                                    {/if}
                                </li>
                                <li>
                                    <a href="{$smarty.server.PHP_SELF}?action=read&id={$advertisements[c]->id}" title="{t}Edit{/t}">
                                        <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onClick="javascript:confirmar(this, '{$advertisements[c]->id}');" title="{t}Delete{/t}">
                                        <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                    </a>
                                </li>
                            </ul>
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
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </form>
</div>
{/block}
