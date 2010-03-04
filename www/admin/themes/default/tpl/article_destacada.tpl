        <table class="adminlist" border=0>
              <tr>
                    <th align="center"></th>
                    <th align="center">T&iacute;tulo</th>
                    <th align="center" style="width:50px;">Visto</th>
                    <th align="center" style="width:50px;">Votos</th>
                    <th align="center" style="width:50px;"><img src="{php}echo($this->image_dir);{/php}coment.png" border="0" alt="Numero comentarios" /></th>
                    <th align="center" style="width:70px;">Fecha</th>
                    {if $category neq 'home'}
                        <th align="center" style="width:50px;">Home</th>
                    {else}
                        <th align="center" style="width:50px;">Secci&oacute;n</th>
                    {/if}
                    <th align="center" style="width:110px;">Publisher</th>
                    <th align="center" style="width:110px;">Last Editor</th>
                    <th align="center" style="width:50px;">Editar</th>
                    <th align="center" style="width:50px;">Archivar</th>
                    <th align="center" style="width:50px;">Despub</th>
                    <th align="center" style="width:50px;">Elim</th>
                </tr>
                <tr>
                    <td colspan="13">
                        <div id="des" class="seccion" class="seccion" style="float:left;width:100%; border:1px solid gray;">
                            <br />
                            {section name="p" loop=$destacado}
                                <table id='tabla{$aux}' name='tabla{$aux}' value="{$destacado[p]->id}" width="100%" class="tabla" style="text-align:center;padding:0px;">
                                    <tr class="{cycle values="row0,row1"}{schedule_class item=$destacado[p]}" style="cursor:pointer;" >
                                        <td style="width:10px;" align="left">
                                            <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.p.iteration}" name="selected_fld[]" value="{$destacado[p]->id}"  style="cursor:pointer;" >
                                        </td>
                                        <td style="" align="left" onClick="javascript:document.getElementById('selected_fld_des_{$smarty.section.p.iteration}').click();" {if $destacado[p]->isScheduled()}onmouseout="UnTip()" onmouseover="Tip('{schedule_info item=$destacado[p]}', SHADOW, true, ABOVE, true, WIDTH, 300)"{/if}>

                                            {is_clone item=$destacado[p]}{$destacado[p]->title|clearslash}
                                        </td>
                                        <td style="width:50px;">
                                            {$destacado[p]->views}
                                        </td>
                                        <td  class='no_view' style="width:50px;" align="center">
                                            {$destacado[p]->rating}
                                        </td>
                                        <td class='no_view' style="width:50px;" align="center">
                                            {$destacado[p]->comment}
                                        </td>
                                        <td style="width:70px;" align="center">
                                            {$destacado[p]->created}
                                        </td>
                                        <td style="width:50px;" align="center">
                                            {if $category neq 'home'}
                                                {if $destacado[p]->in_home == 1}
                                                     <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/></a>
                                                {elseif $destacado[p]->in_home == 2}
                                                        <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" title="No Sugerir en home">
                                                            <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="No Sugerir en home" /></a>
                                                    {else}
                                                        <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" title="Sugerir en home">
                                                            <img class="inhome" src="{$params.IMAGE_DIR}home_no.png" border="0" alt="Sugerir en home" /></a>
                                                    {/if}
                                            {else}
                                                  {$destacado[p]->category_name}
                                            {/if}
                                        </td>
                                        <td  class='no_view' style="width:110px;" align="center">
                                                   {$destacado[p]->publisher}
                                        </td>
                                        <td  class='no_view' style="width:110px;" align="center">
                                                   {$destacado[p]->editor}
                                        </td>
                                        <td style="width:50px;" align="center">
                                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$destacado[p]->id}');" title="Editar">
                                                <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
                                        </td>
                                        <td style="width:50px;" align="center">
                                            <a href="?id={$destacado[p]->id}&amp;action=change_status&amp;status=0&amp;category={$category}" onClick="javascript:confirm('¿Está seguro de enviarlo a hemeroteca?');" title="Archivar">
                                                <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
                                        </td>
                                        <td style="width:50px;" align="center">
                                            {if $category neq 'home'}
                                                {if $destacado[p]->frontpage == 1}
                                                    <a href="?id={$destacado[p]->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada" alt="Quitar de portada">
                                                        <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de home" /></a>
                                                {else}
                                                    <a href="?id={$destacado[p]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada" alt="Publicar en portada">
                                                        <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en home" /></a>
                                                {/if}
                                            {else}
                                                    <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                                            {/if}
                                        </td>

                                        <td style="width:50px;" align="center">
                                            <a href="#" onClick="javascript:delete_article('{$destacado[p]->id}','{$category}',0);" title="Eliminar">
                                                <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                                        </td>
                                    </tr>
                                </table>
                            {sectionelse}
                                <table>
                                    <tr><td align="center" colspan=6><br /><p><h2><b>Ninguna noticia como cabecera</b></h2></p></td></tr>
                                </table>
                            {/section}
                        </div>
                     </td>
                </tr>
            </table>
            <br />