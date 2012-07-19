{extends file="base/admin.tpl"}

{block name="footer-js" append}
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
<form action="{url name=admin_ads}" method="get" name="formulario" id="formulario">
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
                    <button name="status" value="0" id="batch-unpublish" type="submit">
                        <img src="{$params.IMAGE_DIR}publish_no.gif" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </button>
                </li>
                <li>
                    <button name="status" value="1" id="batch-publish" type="submit">
                        <img src="{$params.IMAGE_DIR}publish.gif" alt="Frontpage" ><br />{t}Publish{/t}
                    </button>
                </li>
                {/acl}
                {acl isAllowed="ADVERTISEMENT_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_ad_create category=$category page=$page}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" alt="{t}New{/t}"><br />{t}New{/t}
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
                <a href="{url name=admin_ads category=0}" {if $category==0}class="active"{/if}>{t}HOMEPAGE{/t} </a>
            </li>
            <li>
                <a href="{url name=admin_ads category=4}" {if $category==4}class="active"{/if}>{t}OPINION{/t} </a>
            </li>
            {include file="menu_categories.tpl" home={url name=admin_ads l=1}}
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
                        <button type="submit">{t}Search{/t}</button>
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
                        {acl isAllowed="ADVERTISEMENT_AVAILA"}
                        <th class="center">{t}Available{/t}</th>
                        {/acl}
                        <th class="center" style="width:70px">{t}Actions{/t}</th>
                    </tr>
                </thead>

                <tbody>
                    {foreach from=$advertisements item=ad}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="text-align:center;">
                            <input type="checkbox" class="minput" id="selected_{$smarty.section.c.iteration}" name="selected_fld[]"
                                value="{$ad->pk_advertisement}" />
                        </td>
                        <td style="">
                            <label>
                                {if $ad->with_script == 1}
                                    <img src="{$params.IMAGE_DIR}iconos/script_code_red.png"
                                         alt="Javascript" title="Javascript" />
                                {elseif $ad->is_flash == 1}
                                    <img src="{$params.IMAGE_DIR}flash.gif" alt="{t}Media flash{/t}"
                                         title="{t}Media flash element (swf){/t}" style="width: 16px; height: 16px;"/>
                                {else}
                                    <img src="{$params.IMAGE_DIR}iconos/picture.png" alt="{t}Media{/t}"
                                         title="{t}Media element (jpg, image, gif){/t}" />
                                {/if}
                                {assign var="type_advertisement" value=$ad->type_advertisement}
                                {$map.$type_advertisement}
                            </label>
                        </td>
                        <td style="">
                            {$ad->title|clearslash}
                        </td>

                        <td style="text-align:center;" class="center">
                            {if $ad->type_medida == 'NULL'} {t}Undefined{/t} {/if}
                            {if $ad->type_medida == 'CLIC'} {t}Clicks:{/t} {$ad->num_clic} {/if}
                            {if $ad->type_medida == 'VIEW'} {t}Viewed:{/t} {$ad->num_view} {/if}
                            {if $ad->type_medida == 'DATE'}
                                {t}Date:{/t} {$ad->starttime|date_format:"%d:%m:%Y"}-{$ad->endtime|date_format:"%d:%m:%Y"}
                            {/if}
                        </td>

                        <td style="text-align:center;">
                            {$ad->num_clic_count|number_format:0:',':'.'}
                        </td>
                        <td style="text-align:center;">
                             {$ad->views|number_format:0:',':'.'}
                        </td>
                        {acl isAllowed="ADVERTISEMENT_AVAILA"}
                        <td class="center">
                            {if $ad->available == 1}
                                <a href="{url name=admin_ad_toggleavailable id=$ad->id category=$category status=0 page=$page}"
                                    title={t}"Published"{/t}>
                                    <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" /></a>
                            {else}
                                <a href="{url name=admin_ad_toggleavailable id=$ad->id category=$category status=1 page=$page}"
                                    title={t escape=off}"Unresolved"{/t}>
                                    <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}" /></a>
                            {/if}
                            </li>
                        </td>
                        {/acl}
                        <td style="text-align:center;" class="center">
                            <div class="btn-group">
                            {acl isAllowed="ADVERTISEMENT_UPDATE"}
                                <a class="btn" href="{url name=admin_ad_show id=$ad->id}" title="{t}Edit{/t}">
                                    <i class="icon-pencil"></i>
                                </a>
                            {/acl}
                            {acl isAllowed="ADVERTISEMENT_DELETE"}
                              <a class="del btn btn-danger"
                                    data-controls-modal="modal-from-dom"
                                    data-url="{url name=admin_ad_delete id=$ad->id}"
                                    data-title="{$ad->title|capitalize}"
                                    href="{url name=admin_ad_delete id=$ad->id}" >
                                    <i class="icon-trash icon-white"></i>
                                </a>
                            {/acl}
                            </ul>
                        </td>

                    </tr>
                    {foreachelse}
                    <tr>
                        <td class="empty" colspan="10">
                            {t}There is no advertisement stored in this section{/t}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>

                <tfoot >
                    <tr class="pagination">
                        <td colspan="8">
                            {$paginacion->links}&nbsp;
                        </td>
                    </tr>
                </tfoot>

            </table>

        </div><!--fin id="$category"-->
    </div> <!--end wrapper-->
    <input type="hidden" id="page" name="page" value="{$page}" />
    <input type="hidden" name="category" id="category" value="{$category}" />

</form>
    <script>
        jQuery('#batch-publish, #batch-unpublish').on('click', function(){
            jQuery('#formulario').attr('action', "{url name=admin_ads_batchpublish}");
            jQuery('#formulario').submit();
            e.preventDefault();
        });
    </script>
    {include file="advertisement/modals/_modalDelete.tpl"}
    {include file="advertisement/modals/_modalBatchDelete.tpl"}
    {include file="advertisement/modals/_modalAccept.tpl"}
{/block}