{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .adminlist td {
    	padding-top:4px;
    	padding-bottom:4px;
    }
    .tooltip-inner {
        max-width:500px !important;
        text-align: justify;
    }
</style>
{/block}

{block name="header-js" prepend}
    <script>
    jQuery(document).ready(function ($){
        $('.sync_with_server').click(function() {
           $('.warnings-validation').html('<div class="ui-blocker"></div><div class="ui-blocker-message"><progress style="width:100%"></progress><br /><br />{t}Downloading articles from EuropaPress, please wait...{/t}</div>');
        });
        $('[rel="tooltip"]').tooltip({ placement: 'bottom' });
    });
    </script>
{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EuropaPress importer{/t} :: {t}Available articles{/t}</h2></div>
        <ul class="old-button">
			<li>
				<a href="{url name=admin_importer_europapress_sync}" class="sync_with_server" title="{t}Sync with server{/t}">
				    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
				</a>
			</li>
			<li>
				<a href="{url name=admin_importer_europapress}" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
				</a>
			</li>

			<li>
				<a href="{url name=admin_importer_europapress_config}" class="admin_add" title="{t}Reload list{/t}">
				    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
				</a>
			</li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    {render_messages}

    <div class="warnings-validation"></div><!-- / -->
    <form action="{url name=admin_importer_europapress}" method="get" name="formulario" id="formulario">

        <div class="table-info clearfix">
            <div>
                <div class="left"><label>Total: {$pagination->_totalItems} articles.</label></div>
                <div class="right form-inline">
                    <label for="username">
                        {t}Filter by title or content{/t}
                        <input id="username" name="filter_title" onchange="this.form.submit();" value="{$smarty.request.filter_title}" class="input-medium search-query"/>
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
                <th>{t}Date{/t}</th>
                <th>{t}Section{/t}</th>
                <th style="width:20px;">{t}Actions{/t}</th>
            </tr>
            {else}
            <tr>
                <th coslpan=5>&nbsp;</th>
            </tr>
            {/if}
        </thead>


        <tbody>
            {section name=c loop=$elements}
            <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >

                <td style="text-align:center;">
                   <img src="{$params.IMAGE_DIR}notifications/level-{$elements[c]->priorityNumber}.png" alt="{t 1=$elements[c]->priorityNumber}Priority %1{/t}" title="{t 1=$elements[c]->priorityNumber}Priority %1{/t}">
                </td>
                <td>
                    <a href="{url name=admin_importer_europapress_show id=$elements[c]->xmlFile|urlencode}"
                        rel="tooltip" data-original-title="{$elements[c]->body|clearslash|regex_replace:"/'/":"\'"|escape:'html'}">
                        {$elements[c]->title}
                    </a>
                </td>
                <td>
                        {$elements[c]->created_time->getTimestamp()|relative_date}
                </td>

                <td>
                    {$elements[c]->category|default:""}
                </td>

                <td class="right">
                    <ul class="action-buttons">
                        <li>
                            <a class="btn btn-mini" href="{url name=admin_importer_europapress_import id=$elements[c]->xmlFile|urlencode}" title="{t}Import{/t}">
                                {t}Import{/t}
                            </a>
                        </li>
                    </ul>
                </td>

               </tr>

            {sectionelse}
            <tr>
                <td colspan=5 class="empty">
                    <h2>
                        <b>{t}There is no elements to import{/t}</b>
                    </h2>
                    <p>{t}Try syncing from server by click over the "Sync with server" button above.{/t}</p>
                </td>
            </tr>
            {/section}
        </tbody>
        <tfoot>
             <tr class="pagination" >
                 <td colspan="5">{$pagination->links|default:""}&nbsp;</td>
             </tr>
        </tfoot>

    </table>

	<input type="hidden" id="action" name="action" value="list" />
	</form>
</div>
{/block}
