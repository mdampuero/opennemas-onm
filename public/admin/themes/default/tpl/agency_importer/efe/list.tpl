{extends file="base/admin.tpl"}

{block name="header-css" append}
    <style type="text/css">
    .adminlist td {
	padding-top:4px;
	padding-bottom:4px;
    }
    </style>
{/block}
{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}EFE importer{/t} :: {t}Available articles{/t}</h2></div>
        <ul class="old-button">
			<li>
				<a href="{$smarty.server.PHP_SELF}?action=sync" class="admin_add" value="{t}Sync with server{/t}" title="{t}Sync with server{/t}">
				<img border="0" src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
				</a>
			</li>
			<li>
				<a href="{$smarty.server.PHP_SELF}" class="admin_add" value="{t}Sync with server{/t}" title="{t}Reload list{/t}">
				<img border="0" src="{$params.IMAGE_DIR}template_manager/refresh48x48.png" title="{t}Sync list  with server{/t}" alt="{t}Reload list{/t}" ><br />{t}Reload list{/t}
				</a>
			</li>

			<li>
				<a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" value="{t}Config Europapress module{/t}" title="{t}Reload list{/t}">
				<img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
				</a>
			</li>
        </ul>
    </div>
</div>
<div class="wrapper-content">
   <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="error">
        <p>
        {t}This module is still in development so keep tuned until finished. For now only works the next:{/t}
        <ul>
            <li>Configuration of module (server and auth)</li>
            <li>Synchronization with server to local temporary folder.</li>
            <li>List all the available news, and see its contents.</li>
            <li>Search news by its title</li>
            <li>News import but attachments</li>
        </ul><!-- / -->
        </p>
    </div><!-- / -->

	{render_messages}

	{if ($message || ($minutes > 10))}
	<div class="notice">
	    <ul>
		{if $minutes > 10}
		<p>
			{if $minutes > 100}
			<span class="red">{t}A long time ago from synchronization.{/t}</span>
			{else}
			<span class="red">{t 1=$minutes}Last sync was %1 minutes ago.{/t}</span>
			{/if}
			{t}Try syncing the news list from server by clicking in "Sync with server" button above{/t}
		</p>
		{/if}
		{render_messages}
	    </ul>
	</div>
	{/if}

	{if (!empty($error))}
	<div class="error">
		 {render_error}
	</div>
	{/if}

	<div id="{$category|default:""}">

	    <table class="adminheading">
		<tr>
		    <th align="left">Total: {$pagination->_totalItems} articles.</th>
		    <th nowrap="nowrap" align="right">
			<label for="username">{t}Filter by title{/t}</label>
			<input id="username" name="filter_title" onchange="this.form.submit();" value="{$smarty.request.filter_title}" />

			<label for="usergroup">{t}and category:{/t}</label>
			<select id="usergroup" name="filter_category" onchange="this.form.submit();">
			     <option value="*">{t}All{/t}</option>
			     {html_options options=$categories selected=$smarty.request.filter_group|default:""}
			</select>

			<input type="hidden" name="page" value="{$smarty.request.page|default:""}" />
			<input type="submit" value="{t}Search{/t}">
		    </th>
		</tr>
	    </table>

	    <table class="listing-table">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th style='width:10px !important;'>{t}Priority{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th align="center">{t}Date{/t}</th>
                    <th style="width:40px;">{t}Tags{/t}</th>
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
                       <img src="{$params.IMAGE_DIR}notifications/level-{if $elements[c]->priority > 4}4{else}{$elements[c]->priority}{/if}.png" alt="{t 1=$elements[c]->priority}Priority %1{/t}" title="{t 1=$elements[c]->priority}Priority %1{/t}">
                    </td>
                    <td onmouseout="UnTip()" onmouseover="Tip('{$elements[c]->body|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, false, ABOVE, false, WIDTH, 800)">
                        <a href="{$smarty.server.PHP_SELF}?action=show&id={$elements[c]->xmlFile|urlencode}" title="{t}Import{/t}">
                            {$elements[c]->title}
                            {if $elements[c]->hasPhotos()}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}">
                            {/if}
                        </a>
                    </td>
                    <td align="center">
                            {$elements[c]->created_time->getTimestamp()|relative_date}
                    </td>

                    <td align="center">
                        <div style="max-width:80px; overflow:hidden;">
                            
                        {foreach from=$elements[c]->tags item=group name=loop1}
                            {$group|implode:", "}
                        {/foreach}
                        </div><!-- / -->
                        
                    </td>

                    <td class="right">
                        <ul class="action-buttons">
                            <li>
                                <a class="publishing" href="{$smarty.server.PHP_SELF}?action=import&id={$elements[c]->xmlFile}" title="Importar">
                               <img border="0" alt="Publicar" src="{$params.IMAGE_DIR}archive_no2.png">
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
                     <td colspan="13" align="center">{$pagination->links|default:""}&nbsp;</td>
                 </tr>
            </tfoot>

	   </table>
	</div>

	<input type="hidden" id="action" name="action" value="list" />
	</form>
</div>
{/block}
