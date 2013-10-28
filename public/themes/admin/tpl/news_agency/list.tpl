{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    .already-imported,
    .already-imported:hover{
        background:url({$params.IMAGE_DIR}/backgrounds/stripe-rows.png) top right repeat;
        background-color:none;
    }
    .tooltip-inner {
        max-width:500px !important;
        text-align: justify;
    }
    </style>
{/block}

{block name="header-js" prepend}
<script type="text/javascript">
    jQuery(document).ready(function ($){
        jQuery('.sync_with_server').on('click',function(e, ui) {
            $('#modal-sync').modal('show');
        });
        $('[rel="tooltip"]').tooltip({ placement: 'bottom', html: true });
    });
</script>
{/block}
{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}News Agency{/t}</h2></div>
        <ul class="old-button">
			<li>
				<a href="{url name=admin_news_agency_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
				    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
				</a>
			</li>
			<li>
				<a href="{url name=admin_news_agency}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
				</a>
			</li>
            <li class="separator"></li>
            {acl isAllowed="IMPORT_NEWS_AGENCY_CONFIG"}
			<li>
				<a href="{url name=admin_news_agency_servers}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
				</a>
			</li>
            {/acl}
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <div class="warnings-validation"></div><!-- / -->

    <form action="{url name=admin_news_agency}" method="GET">

    	{render_messages}

        <div class="table-info clearfix">
            <div class="left"><strong>{t 1=$pagination->_totalItems}%1 articles{/t}</strong></div>
            <div class="right form-inline">
                <input type="search" id="username" name="filter_title"value="{$smarty.request.filter_title}" class="input-medium" placeholder="{t}Filter by title or content{/t}"/>

                <div class="input-append">
                    <label for="usergroup">
                        {t}and in{/t}
                        <select id="source" name="filter_source">
                            <option value="*">{t}All sources{/t}</option>
                            {html_options options=$source_names selected=$selectedSource|default:""}
                        </select>
                    </label>

                    <button type="submit" class="btn"><i class="icon-search"></i></button>
                </div>
            </div>
        </div>

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th class="right" style='width:10px !important;'>{t}Priority{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Attachments{/t}</th>
                    <th class="center">{t}Origin{/t}</th>
                    <th class="center" style='width:10px !important;'>{t}Date{/t}</th>
                    <th class="right" style="width:20px;">{t}Actions{/t}</th>
                </tr>
                {/if}
            </thead>

            <tbody>
                {foreach name=c from=$elements item=element}
                <tr class="{if is_array($already_imported) && in_array($element->urn,$already_imported)}already-imported{/if}"  style="cursor:pointer;" >
                    <td  class="right">
                        {if $element->priority <= 1}
                        <span class="badge badge-important">{t}Urgent{/t}</span>
                        {elseif $element->priority == 2}
                        <span class="badge badge-warning">{t}Important{/t}</span>
                        {elseif $element->priority == 3}
                        <span class="badge badge-info">{t}Normal{/t}</span>
                        {else}
                        <span class="badge">{t}Basic{/t}</span>
                        {/if}
                    </td>
                    <td >
                        <a href="{url name=admin_news_agency_show source_id=$element->source_id id=$element->xmlFile|urlencode}" rel="tooltip" data-original-title="{$element->body|clearslash|regex_replace:"/'/":"\'"|escape:'html'|truncate:600:"..."}">
                            {$element->title}
                        </a>
                        <div class="tags">
                            {$element->tags|implode:", "}
                        </div>
                    </td>

                    <td>
                        {if $element->hasPhotos()}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}">
                            {count($element->getPhotos())}
                        {/if}
                        {if $element->hasVideos()}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With video{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $element->hasPhotos() && false}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/polls.png" alt="[{t}With files{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $element->hasPhotos() && false}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/article16x16.png" alt="[{t}With documentary modules{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}
                    </td>
                    <td class="nowrap center">
                        <span class="label" style="background-color:{$servers[$element->source_id]['color']};">{$servers[$element->source_id]['name']}</span>
                    </td>
                    <td class="nowrap center">
                        <span title="{date_format date=$element->created_time}">{date_format date=$element->created_time}</span>
                    </td>

                    <td class="nowrap">
                        <ul class="btn-group">
                            <li>
                                <a class="btn btn-small" href="{url name=admin_news_agency_pickcategory source_id=$element->source_id id=$element->xmlFile|urlencode}" title="{t}Import{/t}">
                                    {t}Import{/t}
                                </a>
                            </li>
                        </ul>
                    </td>

                </tr>
                {foreachelse}
                <tr>
                    <td colspan=6 class="empty">
                        <h2>
                            <b>{t}There is no elements to import{/t}</b>
                        </h2>
                        <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="center">
                        <div class="pagination">
                            {$pagination->links|default:""}
                        </div>
                     </td>
                 </tr>
            </tfoot>

        </table>
	</form>
</div>
{include file="news_agency/modals/_modal_sync_dialog.tpl"}
{/block}
