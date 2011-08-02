<table class="adminlist">
    <thead>
        <th class="title" style="width:50%;">{t}Title{/t}</th>
        <th align="center" style="width:30px;">{t}Type{/t}</th>
        <th align="center" style="width:50px;">{t}Category{/t}</th>
        <th align="center" style="width:50px;">{t}Creation date{/t}</th>
        <th align="center" style="width:20px;">{t}Status{/t}</th>
        <th align="center" style="width:50px;">{t}Actions{/t}</th>
    </thead>
    <tbody>
        {section name=c loop=$arrayResults}
        <tr>
            <td style="padding:10px;"><font size="2">{$arrayResults[c].titule|clearslash}</font><br>
                {if $arrayResults[c].content_type neq 'comment'}
                    <font size="1"><b>{t}Metatags:{/t}</b>  {$arrayResults[c].metadata|clearslash}</font>
                {/if}
            </td>
            <td  align="center">
                {$arrayResults[c].type}
            </td>
            <td align="center">
                {$arrayResults[c].catName}
            </td>
            <td align="center">
                {$arrayResults[c].created}
            </td>
            <td align="center">
                {if ($arrayResults[c].in_litter == 1)}
                    <img src="{$params.IMAGE_DIR}trash.png" border="0" alt="En Papelera" title="En Papelera"/>
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
            <td align="center">
                <ul class="action-buttons">
                    <li>
                        {assign var="ct" value=$arrayResults[c].content_type}
                        {if $arrayResults[c].content_type eq 'photo'}
                                <a href="/admin/{$type2res.$ct}?action=image_data&id={$arrayResults[c].id}&category={$arrayResults[c].category}&desde=search&stringSearch={$smarty.request.stringSearch}&page={$smarty.request.page|default:0}" title="Editar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        {elseif $arrayResults[c].content_type eq 'widget'}
                            <a href="/admin/{$type2res.$ct}?action=edit&id={$arrayResults[c].id}&stringSearch={$smarty.request.stringSearch}&desde=search&page={$smarty.request.page|default:0}" title="Editar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        {else}
                            <a href="/admin/{$type2res.$ct}?action=read&id={$arrayResults[c].id}&stringSearch={$smarty.request.stringSearch}&desde=search&page={$smarty.request.page|default:0}" title="Editar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        {/if}
                    </li>
                    <li>
                        {if ($arrayResults[c].in_litter == 1)}
                            <a href="/admin/controllers/trash.php?action=no_in_litter&desde=search&id={$arrayResults[c].id}&category={$arrayResults[c].category}" title="{t}Restore from trash{/t}">
                               <img src="{$params.IMAGE_DIR}trash_no.png" border="0" width="24" alt="{t}Restore from trash{/t}" title="{t}Restore from trash{/t}" />
                            </a>
                       {else}
                           {if ($arrayResults[c].type == 'Articulo')}
                           {if ($arrayResults[c].available == 1) && ($arrayResults[c].content_status == 0)}
                               <a href="/admin/{$type2res.$ct}?&action=change_status&status=1&desde=search&id={$arrayResults[c].id}&category={$arrayResults[c].category}" title="{t}Restore from library{/t}">
                                     <img src="{$params.IMAGE_DIR}archive_no2.png" border="0" alt="Recuperar Hemeroteca" title="Recuperar de Hemeroteca"/>
                                 </a>
                           {/if}
                           {else}
                               X
                           {/if}
                      {/if}
                    </li>
                </ul>

          </td>
      </tr>

      {sectionelse}
      <tr>
          <td align="center" colspan=4><br><br><p><h2><b>{t}There isn't any existent elements that matches your search criteria{/t}</b></h2></p><br><br></td>
      </tr>
      {/section}
    </tbody>

    <tfoot>
        <td colspan="8" class="pagination">
            {$pagination}
        </td>
    </tfoot>
</table>
