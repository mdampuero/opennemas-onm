<table id='tabla{$aux}' name='tabla{$aux}' value="{$item->id}" data="{$item->content_type}" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
    <tr class="row1{schedule_class item=$item}" style="cursor:pointer;">
        <td style="text-align: left; width:10px;">
            <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$item->id}"  style="cursor:pointer;" />
        </td>
         <td style="text-align: left;"  onmouseout="UnTip()" onmouseover="Tip('<b>Creado:</b>{$item->created}<br /><b>Vistos:</b>{$item->views}<br /><b>Votos:</b>{$item->rating}<br /><b>Comentarios:</b>{$item->comment}<br /><b>Publisher:</b>{$item->publisher}<br /><b>Last Editor:</b>{$item->editor}<br />{schedule_info item=$item}', SHADOW, true, ABOVE, true, WIDTH, 300)" onClick="javascript:document.getElementById('selected_{$placeholder}_{$aux}').click();">

            {is_clone item=$item}{$item->title|clearslash}
        </td>
        {if $category neq 'home'}
            <td class="un_no_view" style="width:20px;" align="center">
                <div class="inhome" style="display:inline;">
                    {if $item->in_home == 1}
                    <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                          <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                          </a>
                    {elseif $item->in_home == 2}
                        <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                        <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" title="No sugerir en home" alt="No sugerir en home"/></a>
                    {else}
                        <a href="?id={$item->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
                    {/if}
                </div>
            </td>
        {else}
            <td style="width:50px;" align="center">
                {$item->category_name}
            </td>
        {/if}
        <td  style="width:80px; text-align:right; padding-right:10px;">
            <ul class="action-buttons">
                <li>
                    <a href="{$smarty.server.PHP_SELF}?id={$item->id}&action=read" title="Editar">
                        <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
                    </a>
                </li>
                <li>
                    <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$item->id}') "  title="Archivar">
                        <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" />
                    </a>
                </li>
                {if $category neq 'home'}
                    <li>
                        {if $item->frontpage == 1}
                        <a href="{$smarty.server.PHP_SELF}?id={$item->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                            <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" />
                        </a>
                        {else}
                        <a href="{$smarty.server.PHP_SELF}?id={$item->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" />
                        </a>
                        {/if}
                    </li>
                    <li>
                        <a href="#" onClick="javascript:delete_article('{$item->id}','{$category}',0);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                    </li>
                {else}
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?id={$item->id}&action=inhome_status&status=0&category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                    </li>
                {/if}
            </ul>
        </td>
    </tr>
</table>
