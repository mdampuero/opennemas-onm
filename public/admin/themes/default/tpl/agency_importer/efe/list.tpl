{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    .adminlist td {
	padding-top:4px;
	padding-bottom:4px;
    }
    .tags-hidden {
        position:absolute;
        width:30px;
        height: 15px;
        margin:0;
        padding:0;
        margin-top:-10px;
        overflow:hidden;
    }
    .tags-hidden:hover {
        margin-top:-15px;
        margin-left:-5px;
        width:auto;
        height:auto;
        overflow:show;
        background:White;
        z-index:999;
        padding:5px;
        box-shadow:0 0 3px rgba(0,0,0,0.2)
    }
    .tags-hidden ul { margin:0; padding:0; display:none; }
    .tags-hidden:hover ul { display:block; }
    .tags-hidden ul li { list-style:none }

    .tags-hidden:hover .list-tags {
        display:none;
    }

    .already-imported,
    .already-imported:hover{
        background:url({$params.IMAGE_DIR}/backgrounds/stripe-rows.png) top right repeat;
        background-color:none;
    }
    </style>
{/block}

{block name="header-js" prepend}
<script type="text/javascript">
    jQuery(document).ready(function (){
        jQuery('.sync_with_server').on('click',function() {
           jQuery('.warnings-validation').html('<div class="ui-blocker"></div><div class="ui-blocker-message"><progress style="width:100%"></progress><br /><br />{t}Downloading articles from EFE, please wait...{/t}</div>');
        });
    });
</script>
{/block}
{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t}Available articles{/t}</h2></div>
        <ul class="old-button">
			<li>
				<a href="{url name=admin_importer_efe_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
				    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
				</a>
			</li>
			<li>
				<a href="{url name=admin_importer_efe}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
				</a>
			</li>
			<li>
				<a href="{url name=admin_importer_efe_config}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
				</a>
			</li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
    <div class="warnings-validation"></div><!-- / -->

    <form action="{url name=admin_importer_efe}" method="GET">

    	{render_messages}

        <div class="table-info clearfix">
            <div>
                <div class="left"><label>{t 1=$pagination->_totalItems}Total: %1 articles.{/t}</label></div>
                <div class="right form-inline">
                    <label for="username">
                        {t}Filter by title or content{/t}
                        <input id="username" name="filter_title" onchange="this.form.submit();" value="{$smarty.request.filter_title}" class="input-medium search-query" />
                    </label>

                    <label for="usergroup">
                        {t}and category:{/t}
                        <select id="usergroup" name="filter_category" onchange="this.form.submit();">
                            <option value="*">{t}All{/t}</option>
                            {html_options options=$categories selected=$smarty.request.filter_group|default:""}
                        </select>
                    </label>

                    <button type="submit" class="btn">{t}Search{/t}</button>
                </div>
            </div>
        </div>

        <table class="listing-table">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th style='width:10px !important;'>{t}Priority{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Attachments{/t}</th>
                    <th>{t}Date{/t}</th>
                    <th style="width:40px;">{t}Tags{/t}</th>
                    <th style="width:20px;">{t}Actions{/t}</th>
                </tr>
                {else}
                <tr>
                    <th coslpan=6>&nbsp;</th>
                </tr>
                {/if}
            </thead>

            <tbody>
                {section name=c loop=$elements}
                <tr class="{if is_array($already_imported) &&  in_array($elements[c]->urn,$already_imported)}already-imported{/if}"  style="cursor:pointer;" >

                    <td style="text-align:center;">
                       <img src="{$params.IMAGE_DIR}notifications/level-{if $elements[c]->priority > 4}4{else}{$elements[c]->priority}{/if}.png" alt="{t 1=$elements[c]->priority}Priority %1{/t}" title="{t 1=$elements[c]->priority}Priority %1{/t}">
                    </td>
                    <td onmouseout="UnTip()" onmouseover="Tip('{$elements[c]->body|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, false, ABOVE, false, WIDTH, 800)">
                        <a href="{url name=admin_importer_efe_show id=$elements[c]->xmlFile|urlencode}" title="{t}Import{/t}">
                            {$elements[c]->title}
                        </a>
                    </td>

                    <td>
                        {if $elements[c]->hasPhotos()}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}">
                        {/if}
                        {if $elements[c]->hasVideos()}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With video{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $elements[c]->hasPhotos() && false}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/polls.png" alt="[{t}With files{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $elements[c]->hasPhotos() && false}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/article16x16.png" alt="[{t}With documentary modules{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}
                    </td>
                    <td>
                        {$elements[c]->created_time->getTimestamp()|relative_date}
                    </td>

                    <td>
                        <div style="position:relative">
                            <div class="tags-hidden" >
                                <span class="list-tags">
                                {foreach from=$elements[c]->tags  key=key item=value name=loop1}
                                    {$key}
                                {/foreach}
                                </span>
                                <ul>
                                {foreach from=$elements[c]->tags item=tag name=loop1}
                                    <li>{$tag}</li>
                                {/foreach}
                                </ul><!-- / -->
                            </div><!-- / -->
                        </div><!-- / -->
                    </td>

                    <td class="right">
                        <ul class="action-buttons">
                            <li>
                                <a class="btn btn-mini" href="{url name=admin_importer_efe_import id=$elements[c]->xmlFile|urlencode}" title="{t}Import{/t}">
                                    {t}Import{/t}
                                </a>
                            </li>
                        </ul>
                    </td>

                </tr>

                {sectionelse}
                <tr>
                    <td colspan=6 class="empty">
                        <h2>
                            <b>{t}There is no elements to import{/t}</b>
                        </h2>
                        <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                    </td>
                </tr>
                {/section}
            </tbody>
            <tfoot>
                 <tr class="pagination">
                     <td colspan="6">{$pagination->links|default:""}&nbsp;</td>
                 </tr>
            </tfoot>

        </table>
	</form>
</div>
{/block}
