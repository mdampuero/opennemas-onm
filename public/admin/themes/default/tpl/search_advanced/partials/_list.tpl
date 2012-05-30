<table class="listing-table">
    <thead>
        <th class="center" style="width:30px;">{t}Type{/t}</th>
        <th class="title" style="width:40%;">{t}Title{/t}</th>
        <th class="center" style="width:50px;">{t}Category{/t}</th>
        <th class="center" style="width:80px;">{t}Creation date{/t}</th>
        <th class="center" style="width:10px;"></th>
        <th class="center" style="width:10px;">{t}Actions{/t}</th>
    </thead>
    <tbody>
        {section name=c loop=$arrayResults}
        <tr>
            <td  class="center">
                {$arrayResults[c].type|htmlentities}
            </td>
            <td style="padding:10px;">
                {$arrayResults[c].titule|clearslash}<br>
                {if $arrayResults[c].content_type neq 'comment'}
                    <strong>{t}Metatags:{/t}</strong>  {$arrayResults[c].metadata|clearslash}
                {/if}
            </td>
            <td class="center">
                {$arrayResults[c].catName}
            </td>
            <td class="center">
                {$arrayResults[c].created}
            </td>
            <td class="center">
                {if ($arrayResults[c].in_litter == 1)}
                    <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera"/>
                {else}
                    {if ($arrayResults[c].type == 'artigo')}
                        {if ($arrayResults[c].available eq 1) && ($arrayResults[c].content_status eq 1)}
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada"/>
                        {elseif ($arrayResults[c].available eq 1) && ($arrayResults[c].content_status eq 0)}
                            <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="{t}In library{/t}" title="{t}In library{/t}" />
                        {else}
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="En Pendientes" />
                        {/if}
                        {elseif $arrayResults[c].content_type eq 'photo'}
                            {if ($arrayResults[c].content_status eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {else}
                            {if ($arrayResults[c].available eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {/if}
                 {/if}
            </td>
            <td class="right">

                <div class="btn-group right">
                    {assign var="ct" value=$arrayResults[c].content_type}
                    {if $arrayResults[c].content_type eq 'photo'}
                    <a class="btn" href="/admin/{$type2res.$ct}?action=image_data&amp;id={$arrayResults[c].id}&amp;category={$arrayResults[c].category}&amp;desde=search&amp;stringSearch={$smarty.request.stringSearch}&amp;page={$smarty.request.page|default:0}" title="Editar">
                        <i class="icon-pencil"></i>
                    </a>
                    {elseif $arrayResults[c].content_type eq 'widget'}
                    <a class="btn" href="/admin/{$type2res.$ct}?action=edit&amp;id={$arrayResults[c].id}&amp;stringSearch={$smarty.request.stringSearch}&amp;desde=search&amp;page={$smarty.request.page|default:0}" title="Editar">
                        <i class="icon-pencil"></i>
                    </a>
                    {else}
                    <a class="btn" href="/admin/{$type2res.$ct}?action=read&amp;id={$arrayResults[c].id}&amp;stringSearch={$smarty.request.stringSearch}&amp;desde=search&amp;page={$smarty.request.page|default:0}" title="Editar">
                        <i class="icon-pencil"></i>
                    </a>
                    {/if}

                    {if ($arrayResults[c].in_litter == 1)}
                        <a href="/admin/controllers/trash.php?action=no_in_litter&amp;desde=search&amp;id={$arrayResults[c].id}&amp;category={$arrayResults[c].category}" title="{t}Restore from trash{/t}">
                            <i class="icon-retweet icon-white"></i> {t}Restore{/t}
                        </a>
                    {else}
                        {if ($arrayResults[c].type == 'Articulo') && ($arrayResults[c].available == 1) && ($arrayResults[c].content_status == 0)}
                            <a href="/admin/{$type2res.$ct}?&amp;action=change_status&amp;status=1&amp;desde=search&amp;id={$arrayResults[c].id}&category={$arrayResults[c].category}" title="{t}Restore from library{/t}">
                                <i class="icon-inbox"></i>
                            </a>
                        {/if}
                    {/if}
                </div>
            </td>
        </tr>

        {sectionelse}
        <tr>
            <td class="empty" colspan=4>
                {t}There isn't any existent elements that matches your search criteria{/t}
            </td>
        </tr>
        {/section}
    </tbody>

    <tfoot>
        <td colspan="8" class="pagination">
            {$pagination->links}
        </td>
    </tfoot>
</table>
