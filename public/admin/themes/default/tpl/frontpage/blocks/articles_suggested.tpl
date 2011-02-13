<table class="adminlist">
    <tr>
        <th align="center"></th>
        <th align="center">T&iacute;tulo</th>
        <th align="center" style="width:60px;">Secci&oacute;n</th>
        <th align="center" style="width:20px;" class="rot270">Editar</th>
        <th align="center" style="width:20px;" class="rot270">Arch</th>
        <th align="center" style="width:20px;" class="rot270">Home</th>


    </tr>
    <tr><td colspan="13">
            <div id="articles-suggested" class="seccion" style="float:left;width:100%;">
                {assign var=aux value='100'}
                {section name=d loop=$suggestedArticles}

<table id='tabla{$aux}' name='tabla{$aux}' value="{$suggestedArticles[d]->id}" data="{$suggestedArticles[d]->content_type}" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
    <tr class="row1{schedule_class item=$suggestedArticles[d]}" style="cursor:pointer;">
        <td style="text-align: left; width:10px;">
            <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$suggestedArticles[d]->id}"  style="cursor:pointer;" />
        </td>
         <td style="text-align: left;"  onmouseout="UnTip()" onmouseover="Tip('<b>Creado:</b>{$suggestedArticles[d]->created}<br /><b>Vistos:</b>{$suggestedArticles[d]->views}<br /><b>Votos:</b>{$suggestedArticles[d]->rating}<br /><b>Comentarios:</b>{$suggestedArticles[d]->comment}<br /><b>Publisher:</b>{$suggestedArticles[d]->publisher}<br /><b>Last Editor:</b>{$suggestedArticles[d]->editor}<br />{schedule_info item=$suggestedArticles[d]}', SHADOW, true, ABOVE, true, WIDTH, 300)" onClick="javascript:document.getElementById('selected_{$placeholder}_{$aux}').click();">

            {is_clone item=$suggestedArticles[d]}{$suggestedArticles[d]->title|clearslash}
        </td>
        {if $category neq 'home'}
            <td class="un_no_view" style="width:20px;" align="center">
                <div class="inhome" style="display:inline;">
                    {if $suggestedArticles[d]->in_home == 1}
                    <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                          <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                          </a>
                    {*elseif $suggestedArticles[d]->in_home == 2}
                        <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                        <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" title="No sugerir en home" alt="No sugerir en home"/></a>
                    *}{else}
                        <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
                    {/if}
                </div>
            </td>
        {else}
            <td style="width:50px;" align="center">
                {$suggestedArticles[d]->category_name}
            </td>
        {/if}
        <td  align="center"  class="un_width" style="text-align: center;width:20px;">
                <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$suggestedArticles[d]->id}');" title="Editar">
                        <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
        </td>
        <td  align="center"  class="un_width" style="width:20px;">
                <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$suggestedArticles[d]->id}') "  title="Archivar">
                   <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
        </td>

        {if $category neq 'home'}
            <td  align="center"  class="un_width" style="width:20px;">
            {if $suggestedArticles[d]->frontpage == 1}
                    <a href="?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                            <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" /></a>
            {else}
                    <a href="?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" /></a>
            {/if}
             </td>
            <td  align="center"  class="un_width"  style="width:20px;">
                <a href="#" onClick="javascript:delete_article('{$suggestedArticles[d]->id}','{$category}',0);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
            </td>
        {else}
             <td  align="center"  class="un_width" style="width:25px;">
                <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
            </td>
        {/if}

    </tr>
</table>

                    {assign var=aux value=$aux+1}
                {/section}

           </div>
        </td>
   </tr>
</table>
