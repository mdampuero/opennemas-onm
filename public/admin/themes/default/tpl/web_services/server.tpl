{extends file="base/admin.tpl"}

{block name="header-css" append}

{/block}

{block name="header-js" prepend}

{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Server side{/t}</h2></div>
        <ul class="old-button">
            <li>
                    <a href="{$smarty.server.PHP_SELF}?action=sync" class="sync_with_server" title="{t}Sync with server{/t}">
                    <img src="{$params.IMAGE_DIR}sync.png" title="{t}Sync list  with server{/t}" alt="{t}Sync with server{/t}" ><br />{t}Sync with server{/t}
                    </a>
            </li>
            <li>
                    <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Reload list{/t}">
                    <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" title="{t}Config Europapress module{/t}" alt="{t}Config Europapress module{/t}" ><br />{t}Config{/t}
                    </a>
            </li>
        </ul>
    </div>
</div>
<div class="wrapper-content">

    <div class="warnings-validation"></div><!-- / -->

    <form action="{$smarty.server.PHP_SELF}" method="get" name="formulario" id="formulario" {$formAttrs|default:""}>

    	{render_messages}

        <table class="adminheading">
    	<tr>
    	    <th>Total articles.</th>
    	    <th >
    		<label for="username">{t}Filter by title or content{/t}</label>
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
                        <a href="{$smarty.server.PHP_SELF}?action=show&amp;id={$elements[c]->xmlFile|urlencode}" title="{t}Import{/t}">
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
                                <a class="publishing" href="{$smarty.server.PHP_SELF}?action=import_select_category&amp;id={$elements[c]->xmlFile}" title="Importar">
                               <img alt="Publicar" src="{$params.IMAGE_DIR}archive_no2.png">
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

    	<input type="hidden" id="action" name="action" value="list" />
	</form>
</div>
{/block}
