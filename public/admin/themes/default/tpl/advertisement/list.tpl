{extends file="base/admin.tpl"}

{block name="footer-js" append}
    {script_tag src="/utilsadvertisement.js"}
    {script_tag src="/AdPosition.js"}
    <script type="text/javascript">
        function submitFilters(frm) {
            $('action').value='list';
            $('page').value = 1;

            frm.submit();
        }
    </script>
{/block}


{block name="content"}
<form action="#" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Advertisement manager{/t}::&nbsp; {if $category eq 0}HOME{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="ADVERTISEMENT_DELETE"}
                <li>
                     <a class="delChecked" data-controls-modal="modal-advertisement-batchDelete" href="#" title="{t}Delete{/t}">
                        <img src="{$params.IMAGE_DIR}trash.png" title="{t}Delete{/t}" alt="{t}Delete{/t}"><br />{t}Delete{/t}
                    </a>
                </li>
                {/acl}
                {acl isAllowed="ADVERTISEMENT_AVAILA"}
                <li>
                    <button value="batchFrontpage" name="buton-batchnoFrontpage" id="buton-batchnoFrontpage" type="submit">
                        <img src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </button>
                </li>
                <li>
                     <button value="batchFrontpage" name="buton-batchFrontpage" id="buton-batchFrontpage" type="submit">
                        <img src="{$params.IMAGE_DIR}publish.gif" title="Frontpage" alt="Frontpage" ><br />{t}Publish{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="ADVERTISEMENT_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.const.SITE_URL}{$smarty.const.ADMIN_DIR}/controllers/advertisement/advertisement.php?action=new&amp;category={$smarty.request.category}&amp;page={$smarty.get.page|default:0}"
                       class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" title="{t}New{/t}" alt="{t}New{/t}"><br />{t}New{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

         {render_messages}
        <ul class="pills clearfix">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;category=0" id="link_home" {if $category==0}class="active"{/if}>{t}HOMEPAGE{/t} </a>
            </li>
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&amp;category=4" id="link_op" {if $category==4}class="active"{/if}>{t}OPINION{/t} </a>
            </li>
            {include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        <div id="{$category}">

            <table class="adminheading">
                <tr>
                    <th class="form-inline">
                        <label for="filter[type_advertisement]">{t}Banner type:{/t}</label>
                        <select name="filter[type_advertisement]" id="filter[type_advertisement]" onchange="submitFilters(this.form);">
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

            <table class="listing-table">
                <thead>
                    <tr>
                        <th  style="width:15px">
                            <input type="checkbox" id="toggleallcheckbox" />
                        </th>
                        <th class="title"  style="width:250px">{t}Type{/t}</th>
                        <th>{t}Title{/t}</th>
                        <th class="center" style="width:30px">{t}Permanence{/t}</th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                        <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                        <th class="center" style="width:70px">{t}Actions{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    {section name=c loop=$advertisements}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="text-align:center;">
                            <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
                                value="{$advertisements[c]->pk_advertisement}" />
                        </td>
                        <td style="">
                            <label>
                                {if $advertisements[c]->with_script == 1}
                                    <img src="{$params.IMAGE_DIR}iconos/script_code_red.png"
                                         alt="Javascript" title="Javascript" />
                                {elseif $advertisements[c]->is_flash == 1}
                                    <img src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}"
                                         title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                                {else}
                                    <img src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}"
                                         title="{t}Media element (jpg, image, gif){/t}" />
                                {/if}
                                {assign var="type_advertisement" value=$advertisements[c]->type_advertisement}
                                {$map.$type_advertisement}
                            </label>
                        </td>
                        <td style="">
                            {$advertisements[c]->title|clearslash}
                        </td>

                        <td style="text-align:center;" class="center">
                            {if $advertisements[c]->type_medida == 'NULL'} {t}Undefined{/t} {/if}
                            {if $advertisements[c]->type_medida == 'CLIC'} {t}Clicks:{/t} {$advertisements[c]->num_clic} {/if}
                            {if $advertisements[c]->type_medida == 'VIEW'} {t}Viewed:{/t} {$advertisements[c]->num_view} {/if}
                            {if $advertisements[c]->type_medida == 'DATE'}
                                {t}Date:{/t} {$advertisements[c]->starttime|date_format:"%d:%m:%Y"}-{$advertisements[c]->endtime|date_format:"%d:%m:%Y"}
                            {/if}
                        </td>

                        <td style="text-align:center;">
                            {$advertisements[c]->num_clic_count|number_format:0:',':'.'}
                        </td>
                        <td style="text-align:center;">
                             {$advertisements[c]->views|number_format:0:',':'.'}
                        </td>
                        <td style="text-align:center;" class="center">
                            <ul class="action-buttons">

                                {acl isAllowed="ADVERTISEMENT_AVAILA"}
                                <li>
                                    {if $advertisements[c]->available == 1}
                                        <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;category={$category}&amp;status=0&amp;&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                            title={t}"Published"{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" /></a>
                                    {else}
                                        <a href="?id={$advertisements[c]->id}&amp;action=available_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}&amp;{$query_string}"
                                            title={t escape=off}"Unresolved"{/t}>
                                            <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}" /></a>
                                    {/if}
                                </li>
                                {/acl}
                                {acl isAllowed="ADVERTISEMENT_UPDATE"}
                                <li>
                                    <a href="{$smarty.server.PHP_SELF}?action=read&amp;id={$advertisements[c]->id}&amp;place={$advertisements[c]->advertisement_placeholder}" title="{t}Edit{/t}">
                                        <img src="{$params.IMAGE_DIR}edit.png" />
                                    </a>
                                </li>
                                {/acl}
                                {acl isAllowed="ADVERTISEMENT_DELETE"}
                                <li>
                                      <a class="del" data-controls-modal="modal-from-dom"
                                           data-id="{$advertisements[c]->id}"
                                           data-title="{$advertisements[c]->title|capitalize}"  href="#" >
                                        <img src="{$params.IMAGE_DIR}trash.png" />
                                    </a>
                                </li>
                                {/acl}
                            </ul>
                        </td>

                    </tr>
                    {sectionelse}
                    <tr>
                        <td class="empty" colspan="10">
                            {t}There is no advertisement stored in this section{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>

                <tfoot >
                    <tr class="pagination">
                        <td colspan="7">
                            {$paginacion->links}&nbsp;
                        </td>
                    </tr>
                </tfoot>

            </table>

        </div><!--fin id="$category"-->
    </div> <!--end wrapper-->
    <input type="hidden" name="category" id="category" value="{$category}" />
    <input type="hidden" id="status" name="status" value="" />
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />

</form>
     <script>
        jQuery('#buton-batchnoFrontpage').on('click', function(){
            jQuery('#formulario').attr('method', "POST");
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "0");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
        jQuery('#buton-batchFrontpage').on('click', function(){
            jQuery('#formulario').attr('method', "POST");
            jQuery('#action').attr('value', "batchFrontpage");
            jQuery('#status').attr('value', "1");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
    {include file="advertisement/modals/_modalDelete.tpl"}
    {include file="advertisement/modals/_modalBatchDelete.tpl"}
    {include file="advertisement/modals/_modalAccept.tpl"}
{/block}