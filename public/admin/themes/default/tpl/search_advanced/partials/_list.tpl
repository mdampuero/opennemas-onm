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
        {foreach name=c from=$contents item=content}
        <tr>
            <td  class="center">
                {$content.type|htmlentities}
            </td>
            <td style="padding:10px;">
                {$content.titule|clearslash}<br>
                {if $content.content_type neq 'comment'}
                    <strong>{t}Metatags:{/t}</strong>  {$content.metadata|clearslash}
                {/if}
            </td>
            <td class="center">
                {$content.catName}
            </td>
            <td class="center">
                {$content.created}
            </td>
            <td class="center">
                {if ($content.in_litter == 1)}
                    <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera"/>
                {else}
                    {if ($content.type == '')}
                        {if ($content.available eq 1) && ($content.content_status eq 1)}
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada"/>
                        {elseif ($content.available eq 1) && ($content.content_status eq 0)}
                            <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="{t}In library{/t}" title="{t}In library{/t}" />
                        {else}
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="En Pendientes" />
                        {/if}
                        {elseif $content.content_type eq 'photo'}
                            {if ($content.content_status eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {else}
                            {if ($content.available eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {/if}
                 {/if}
            </td>
            <td class="right">

                <div class="btn-group right">
                    <a class="btn" href="{url name="admin_"|cat:$content['content_type']|cat:"_show" id=$content.id}" title="Editar">
                        <i class="icon-pencil"></i>
                    </a>
                </div>
            </td>
        </tr>

        {foreachelse}
        <tr>
            <td class="empty" colspan=4>
                {t}There isn't any existent elements that matches your search criteria{/t}
            </td>
        </tr>
        {/foreach}
    </tbody>

    <tfoot>
        <td colspan="8" class="pagination">
            {$pagination->links}
        </td>
    </tfoot>
</table>
