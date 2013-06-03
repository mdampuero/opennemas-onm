<table class="table table-hover table-condensed">
    <thead>
        <th class="title">{t}Title{/t}</th>
        <th class="center" style="width:10px;"></th>
        <th class="right" style="width:10px;">{t}Actions{/t}</th>
    </thead>
    <tbody>
        {foreach $contents as $content}
        <tr>
            <td style="padding:10px;">
                <strong>[{$content->content_type_l10n_name}] {$content->title|clearslash}</strong>
                <br>
                {if $content->content_type neq 'comment'}
                <img src="{$params.IMAGE_DIR}/tag_red.png" alt="">  {$content->metadata|clearslash}
                {/if}<br>
                <strong>{t}Category{/t}:</strong> {$content->category_name}
                <br>
                <strong>{t}Created{/t}:</strong> {$content->created}
            </td>
            <td class="center">
                {if ($content->in_litter == 1)}
                    <img src="{$params.IMAGE_DIR}trash.png" height="16px" alt="En Papelera" title="En Papelera"/>
                {else}
                    {if ($content->type == '')}
                        {if ($content->available eq 1) && ($content->content_status eq 1)}
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicada" title="Publicada"/>
                        {elseif ($content->available eq 1) && ($content->content_status eq 0)}
                            <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="{t}In library{/t}" title="{t}In library{/t}" />
                        {else}
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="En Pendientes" />
                        {/if}
                        {elseif $content->content_type eq 'photo'}
                            {if ($content->content_status eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {else}
                            {if ($contentvavailable eq 1)}
                                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" title="{t}Published{/t}" />
                            {else}
                                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="{t}In pending{/t}" title="{t}In pending{/t}" />
                            {/if}
                        {/if}
                 {/if}
            </td>
            <td class="right">
                <div class="btn-group right">
                    <a class="btn" href="{url name="admin_"|cat:$content->content_type_name|cat:"_show" id=$content->id}" title="Editar">
                        <i class="icon-pencil"></i>
                    </a>
                </div>
            </td>
        </tr>

        {foreachelse}
        <tr>
            <td class="empty" colspan=3>
                {t}There isn't any existent elements that matches your search criteria{/t}
            </td>
        </tr>
        {/foreach}
    </tbody>

    <tfoot>
        <td colspan="3" class="center">
            <div class="pagination">
                {$pagination->links}
            </div>
        </td>
    </tfoot>
</table>
