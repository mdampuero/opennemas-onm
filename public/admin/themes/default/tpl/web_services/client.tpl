{extends file="base/admin.tpl"}

{block name="header-css" append}

{/block}

{block name="header-js" prepend}

{/block}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Sync Manager{/t} :: {t}Client side{/t}</h2></div>
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
    	</tr>
        </table>

        <table class="listing-table">
            <thead>
                <tr>
                {if count($elements) >0}
                    <th style='width:10px !important;'>{t}Type{/t}</th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Attachments{/t}</th>
                    <th>{t}Date{/t}</th>
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
                <tr>
                    <td style="text-align:center;">
                    </td>
                    <td onmouseout="UnTip()" onmouseover="Tip('{$elements[c]->item->body|regex_replace:"/[\r\t\n]/":" "|clearslash|regex_replace:"/'/":"\'"|escape:'html'}', SHADOW, false, ABOVE, false, WIDTH, 800)">
                        <a href="{$smarty.server.PHP_SELF}?action=show&amp;id={$elements[c]->item->id}" title="{t}Import{/t}">
                            {$elements[c]->item->title}
                        </a>
                    </td>

                    <td>
                        {if $elements[c]->item->img1 neq null}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/gallery16x16.png" alt="[{t}With image{/t}] " title="{t}This new has attached images{/t}">
                        {/if}
                        {if $elements[c]->item->fk_video neq '0'}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/video16x16.png" alt="[{t}With video{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $elements[c]->item->img2 neq null}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/polls.png" alt="[{t}With files{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}

                        {if $elements[c]->item->fk_video2 neq '0'}
                            <img src="{$params.IMAGE_DIR}template_manager/elements/article16x16.png" alt="[{t}With documentary modules{/t}] " title="{t}This new has attached videos{/t}">
                        {/if}
                    </td>
                    <td>
                        {$elements[c]->item->created}
                    </td>

                    <td class="right">
                        <ul class="action-buttons">
                            <li>
                                <a class="publishing" href="#" title="Importar">
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

    	<input type="hidden" id="action" name="action" value="" />
	</form>
</div>
{/block}
