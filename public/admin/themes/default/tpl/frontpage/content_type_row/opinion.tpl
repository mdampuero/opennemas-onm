<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->pk_opinion}" data="Opinion" width="100%" class="tabla">
    <tr class="row1{schedule_class item=$item}" style="cursor:pointer;">

        <td style="text-align: left; width:10px;">
            <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" />
        </td>
        <td align="left" style="text-align: left;"  onmouseout="UnTip()" onmouseover="Tip('<b>Creado:</b>{$item->created}<br /><b>Vistos:</b>{$item->views}<br /><b>Votos:</b>{$item->rating}<br /><b>Comentarios:</b>{$item->comment}<br /><b>Publisher:</b>{$item->publisher}<br /><b>Last Editor:</b>{$item->editor}<br />{schedule_info item=$item}', SHADOW, true, ABOVE, true, WIDTH, 300)" onClick="javascript:document.getElementById('selected_{$placeholder}_{$aux}').click();">

            <strong>{t}OPINION{/t} - {$item->author}: </strong>{$item->title}
        </td>
        {if $category neq 'home'}
            <td class="un_no_view" style="width:20px;" align="center">
                <div class="inhome" style="display:inline;">
                    {if $item->in_home == 1}
                    <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                          <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                          </a>
                    {elseif $item->in_home == 2}
                        <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                        <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" title="No sugerir en home" alt="No sugerir en home"/></a>
                    {else}
                        <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
                    {/if}
                </div>
            </td>
        {/if}
        <td align="center" style="width:20px">
            <a href="controllers/opinion/opinion.php?action=read&id={$item->id}" title="{t}Edit{/t}"><img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
        </td>
        <td  align="center"  class="un_width" style="width:20px;">
                <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$item->id}') "  title="Archivar">
                   <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
        </td>

        {if $category neq 'home'}
            <td  align="center"  class="un_width" style="width:20px;">
            {if $item->frontpage == 1}
                    <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                            <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" /></a>
            {else}
                    <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" /></a>
            {/if}
             </td>
             <td  align="center"  class="un_width"  style="width:20px;">
                <a href="#" onClick="javascript:delete_opinion('{$item->id}',0);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
            </td>
        {else}
             <td  align="center"  class="un_width" style="width:25px;">
                <a href="controllers/opinion/opinion.php?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
            </td>
        {/if}
    </tr>
</table>
