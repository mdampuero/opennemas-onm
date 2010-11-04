<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->id}" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
    <tr class="row1{schedule_class item=$item}" style="cursor:pointer;" >
        <td style="text-align: left;width:10px;" >
            <input type="checkbox" class="minput" pos={$aux} id="selected_fld_odd_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" >
        </td>
         <td style="text-align: left;" onmouseout="UnTip()" onmouseover="Tip('<b>Votos:</b>{$item->rating}<br /><b>Comentarios:</b>{$item->comment}<br /><b>Publisher:</b>{$item->publisher}<br /><b>Last Editor:</b>{$item->editor}<br />{schedule_info item=$item}', SHADOW, true, ABOVE, true, WIDTH, 300)"onClick="javascript:document.getElementById('selected_fld_odd_{$aux}').click();">
            
            {is_clone item=$item}{$item->title|clearslash}
        </td>
            <td style="width:50px;">
                {$item->views}
            </td>
            <td  class='no_view' style="width:50px;" align="center">
                {$item->rating}
            </td>
            <td class='no_view' style="width:50px;" align="center">
                {$item->comment}
            </td>
            <td style="width:70px;" align="center">
                {$item->created}
            </td>
            <td style="width:50px;" align="center">

                    {if $item->in_home == 1}
                         <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/></a>
                    {elseif $item->in_home == 2}
                            <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" title="No Sugerir en home">
                                <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="No Sugerir en home" /></a>
                        {else}
                            <a href="?id={$item->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" title="Sugerir en home">
                                <img class="inhome" src="{$params.IMAGE_DIR}home_no.png" border="0" alt="Sugerir en home" /></a>
                        {/if}

            </td>
            <td  class='no_view' style="width:110px;" align="center">
                       {$item->publisher}
            </td>
            <td  class='no_view' style="width:110px;" align="center">
                       {$item->editor}
            </td>
            <td style="width:50px;" align="center">
                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$item->id}');" title="Editar">
                    <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
            </td>
            <td style="width:50px;" align="center">
                <a href="?id={$item->id}&amp;action=change_status&amp;status=0&amp;category={$category}" onClick="javascript:confirm('¿Está seguro de enviarlo a hemeroteca?');" title="Archivar">
                    <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
            </td>
            <td style="width:50px;" align="center">
                {if $category neq 'home'}
                    {if $item->frontpage == 1}
                        <a href="?id={$item->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada" alt="Quitar de portada">
                            <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de home" /></a>
                    {else}
                        <a href="?id={$item->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada" alt="Publicar en portada">
                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en home" /></a>
                    {/if}
                {else}
                        <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                {/if}
            </td>

        <td  align="center"  style="width:30px;">
                <a href="#" onClick="javascript:delete_article('{$item->id}','{$category}',0);" title="Eliminar" alt="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
        </td>
    </tr>
</table>