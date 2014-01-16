{extends file="base/admin.tpl"}


{block name="content"}
<form action="{url name=admin_ads}" method="get" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title">
                <h2>{t}Advertisements{/t} :: </h2>
                <div class="section-picker">
                    <div class="title-picker btn"><span class="text">{if $category==0}{t}HOMEPAGE{/t}{elseif $category==4}{t}OPINION{/t}{else}{$datos_cat[0]->title}{/if}</span> <span class="caret"></span></div>
                    <div class="options">
                        <h4>{t}Special elements{/t}</h4>
                        <a href="{url name=admin_ads category=0}" {if $category==0}class="active"{/if}>{t}HOMEPAGE{/t} </a>
                        <a href="{url name=admin_ads category=4}" {if $category==4}class="active"{/if}>{t}OPINION{/t} </a>

                        {include file="common/drop_down_categories.tpl" home={url name=admin_ads l=1} hide_all=true}
                    </div>
                </div>
            </div>
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
                {acl isAllowed="ALBUM_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{url name=admin_ads_config}" title="{t}Config ads module{/t}">
                            <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Settings{/t}
                        </a>
                    </li>
                {/acl}
                {acl isAllowed="ADVERTISEMENT_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_ad_create category=$category page=$page filter=$filter}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" alt="{t}New{/t}"><br />{t}New{/t}
                    </a>
                </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div class="table-info clearfix">
            <div class="pull-right form-inline">
                <label for="filter[type_advertisement]">{t}Banner type:{/t}</label>
                <select name="filter[type_advertisement]" id="filter[type_advertisement]">
                    {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type_advertisement)}
                        {assign var=filterType value=""}
                    {else}
                        {assign var=filterType value=$smarty.request.filter.type_advertisement|default:""}
                    {/if}
                    {html_options options=$filter_options.type_advertisement selected=$filterType}
                </select>
                &nbsp;&nbsp;&nbsp;
                <label>{t}Status:{/t}</label>
                <select name="filter[available]"x>
                    {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type_advertisement)}
                        {assign var=filterAvailable value=""}
                    {else}
                        {assign var=filterAvailable value=$smarty.request.filter.available|default:""}
                    {/if}
                    {html_options options=$filter_options.available selected=$filterAvailable}
                </select>
                 &nbsp;&nbsp;&nbsp;
                <label>{t}Type:{/t}</label>
                <div class="input-append">
                    <select name="filter[type]">
                        {if !isset($smarty.request.filter) && !isset($smarty.request.filter.type)}
                            {assign var=filterType value=""}
                        {else}
                            {assign var=filterType value=$smarty.request.filter.type|default:""}
                        {/if}ubmit"><i class="icon-search"></i></button>
                        {html_options options=$filter_options.type selected=$filterType}
                    </select>
                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th  style="width:15px">
                        <input type="checkbox" class="toggleallcheckbox" />
                    </th>
                    <th class="title"  style="width:250px">{t}Type{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th class="center" style="width:30px">{t}Permanence{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}clicked.png" alt="{t}Clicks{/t}" title="{t}Clicks{/t}"></th>
                    {acl isAllowed="ADVERTISEMENT_AVAILA"}
                    <th class="center" style="width:40px;">{t}Available{/t}</th>
                    {/acl}
                    <th class="right" style="width:70px">{t}Actions{/t}</th>
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
                            {$map.$type_advertisement['name']}
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
                    {acl isAllowed="ADVERTISEMENT_AVAILA"}
                    <td class="center" style="width:40px;">
                        {if $ad->available == 1}
                            <a href="{url name=admin_ad_toggleavailable id=$ad->id category=$category status=0 page=$page filter=$filter}"
                                title={t}"Published"{/t}>
                                <img src="{$params.IMAGE_DIR}publish_g.png" alt="{t}Published{/t}" /></a>
                        {else}
                            <a href="{url name=admin_ad_toggleavailable id=$ad->id category=$category status=1 page=$page filter=$filter}"
                                title={t escape=off}"Unresolved"{/t}>
                                <img src="{$params.IMAGE_DIR}publish_r.png" alt="{t}Pending{/t}" /></a>
                        {/if}
                        </li>
                    </td>
                    {/acl}
                    <td class="right">
                        <div class="btn-group">
                        {acl isAllowed="ADVERTISEMENT_UPDATE"}
                            <a class="btn" href="{url name=admin_ad_show id=$ad->id  category=$category page=$page filter=$filter}" title="{t}Edit{/t}">
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
                <tr>
                    <td colspan="8" class="center">
                        <div class="pagination">
                            {$pagination->links}
                        </div>
                    </td>
                </tr>
            </tfoot>

        </table>

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
