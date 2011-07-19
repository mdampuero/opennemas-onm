<table class="adminlist">
    <thead>
        <th class="title">Title</th>
        <th align="center">Type</th>
        <th align="center">Section</th>
        <th align="center">Creation date</th>
        <th align="center">Status</th>
        <th align="center">Edit</th>
        <th align="center">Restore</th>
        <!--<th align="center">View</th>-->
        <!--<th align="center">Notify</th>-->
    </thead>
    <tbody>
        {section name=c loop=$arrayResults}
        <tr {cycle values="class=row0,class=row1"}>
            <td style="padding:10px;width:50%;"><font size="2">{$arrayResults[c].titule|clearslash}</font><br>
                {if $arrayResults[c].content_type neq 'comment'}
                    <font size="1"><b>{t}Metatags:{/t}</b>  {$arrayResults[c].metadata|clearslash}</font>
                {/if}
            </td>
            <td style="width:15%;" align="center">
                {$arrayResults[c].type}
            </td>
            <td style="width:15%;" align="center">
                {$arrayResults[c].catName}
            </td>
            <td style="width:15%;" align="center">
                {$arrayResults[c].created}
            </td>
            <td style="padding:10px;width:10%;" align="center">
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
            <td style="width:10%;" align="center">
                {assign var="ct" value=$arrayResults[c].content_type}
                {if $arrayResults[c].content_type eq 'photo'}
                        <a href="/admin/{$type2res.$ct}?action=image_data&id={$arrayResults[c].id}&category={$arrayResults[c].category}&desde=search&stringSearch={$smarty.request.stringSearch}&page={$smarty.request.page|default:0}" title="Editar">
                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                {else}
                    <a href="/admin/{$type2res.$ct}?action=read&id={$arrayResults[c].id}&stringSearch={$smarty.request.stringSearch}&desde=search&page={$smarty.request.page|default:0}" title="Editar">
                    <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                {/if}
            </td>

            <td style="width:10%;" align="center">
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
          </td>
          <!--<td style="width:10%;" align="center">-->
          <!--    <a href="#" target="_blank" accesskey="P"-->
          <!--       onmouseover="return escape('<u>P</u>revisualizar');"-->
          <!--       onclick="myLightWindow.activateWindow({ href: '{$arrayResults[c].uri}',title: 'Previsualización',author: {$smarty.const.SITEFULL_NAME}});return false;" >-->
          <!--      <img border="0" src="{$params.IMAGE_DIR}preview_small.png" title="Previsualizar" alt="Previsualizar" />-->
          <!--    </a>-->
          <!--</td>-->
          <!--<td style="width:10%;" align="center">-->
          <!--    <a href="#" accesskey="N'" onmouseover="return escape('<u>N</u>otificar');" onclick="send_notify('{$arrayResults[c].id}','confirm_notify');" >-->
          <!--        <img border="0" src="{$params.IMAGE_DIR}file_alert.png" title="Notificar" alt="Notificar" />-->
          <!--    </a>-->
          <!--</td>-->
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
