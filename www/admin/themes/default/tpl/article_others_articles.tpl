<table class="adminlist">
    <tr>
        <th align="center"></th>
        <th align="center">T&iacute;tulo</th>
        <th align="center" style="width:50px;">Visto</th>
        <th align="center" style="width:50px;">Votos</th>
        <th align="center" style="width:50px;"><img src="{php}echo($this->image_dir);{/php}coment.png" border="0" alt="Numero comentarios" /></th>
        <th align="center" style="width:70px;">Fecha</th>       
        <th align="center" style="width:110px;">Publisher</th>
        <th align="center" style="width:110px;">Last Editor</th>
        {if $other_category eq 'suggested'}
            <th align="center" style="width:60px;">Secci&oacute;n</th>
        {/if}
        <th align="center" style="width:20px;" class="rot270">Editar</th>
        <th align="center" style="width:20px;" class="rot270">Arch</th>
        <th align="center" style="width:20px;" class="rot270">Home</th>
        <th align="center" style="width:20px;" class="rot270">Elim</th>


    </tr>
    <tr><td colspan="13">
     <div id="artic" class="seccion" style="float:left;width:100%;">
            <div id="art" class="seccion" style="float:left;width:100%;"> <br />
                {assign var=aux value='100'}
                {section name=d loop=$articles}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$articles[d]->id}" class="tabla" style="text-align:center;padding:0px;">
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >                            
                             <td align="left"  style="width:10px;" >
                                <input type="checkbox" class="minput" id="selected_fld_art_{$smarty.section.d.iteration}" name="no_selected_fld[]" value="{$articles[d]->id}" style="cursor:pointer;" >&nbsp;
                            </td>
                            <td style="padding:2px;" align="left" onClick="javascript:document.getElementById('selected_fld_art_{$smarty.section.d.iteration}').click();">
                                
                                {is_clone item=$articles[d]}{$articles[d]->title|clearslash}
                            </td>
                            <td  class='no_width' style="text-align:center;width:50px;"  align="center">
                                {$articles[d]->views}
                            </td>
                            <td style="width:50px;" class="no_view" align="center">
                                 {$articles[d]->rating}
                            </td>
                            <td style="width:50px;"  class="no_view" align="center">
                                 {$articles[d]->comment}
                            </td>
                            <td style="width:70px;" align="center">
                                {$articles[d]->created}
                            </td>
                            {if $other_category neq 'suggested'}
                                <td>
                                    <div class="noinhome" style="display:none;">
                                        {if $articles[d]->in_home == 1}
                                            <a title="Publicada en home">
                                                <img class="inhome" src="{php}echo($this->image_dir);{/php}gohome.png" border="0" alt="Publicada en home" />
                                            </a>
                                        {else}
                                            {if $articles[d]->in_home == 2}
                                                <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" title="Publicar en home">
                                                    <img class="inhome" src="{php}echo($this->image_dir);{/php}gosuggesthome.png" border="0" alt="Publicar en home" />
                                                </a>
                                            {else}
                                                <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" title="Sugerir en home">
                                                    <img class="inhome" src="{php}echo($this->image_dir);{/php}home_no.png" border="0" alt="Sugerir en home" />
                                                </a>
                                            {/if}
                                        {/if}
                                     </div>                               
                                </td>
                            {/if}
                            <td class='no_view' style="width:110px;" align="center">
                                 {$articles[d]->publisher}
                            </td>
                            <td class='no_view' style="width:110px;" align="center">
                                 {$articles[d]->editor}
                            </td>
                            {if $other_category eq 'suggested'}
                                 <td style="width:60px;" align="center">
                                    {$articles[d]->category_name}
                                </td>
                            {/if}
                                <td class='no_width' style="width:20px;" align="center">
                                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$articles[d]->id}');" title="Editar">
                                        <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" alt="Editar" />
                                    </a>
                                </td>
                                <td class='no_width' style="width:30px;" align="center">
                                    <a href="?id={$articles[d]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Archivar">
                                        <img src="{php}echo($this->image_dir);{/php}save_hemeroteca_icon.png" border="0" alt="Archivar" />
                                    </a>
                                </td>
                                 {if $other_category neq 'suggested'}
                                    <td class='no_width' style="width:23px;" align="center">
                                         <a href="?id={$articles[d]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="No en portada">
                                            <img class="noportada" src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="No en portada" />
                                        </a>
                                        
                                     {*   <img class="noportada" src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="No en portada" /> *}
                                        
                                    </td>
                                {else}
                                     <td class='no_width' style="width:20px;" align="center">
                                           <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" title="Poner en portada"  alt="Poner en portada">
                                                <img class="inhome" src="{php}echo($this->image_dir);{/php}gosuggesthome.png" border="0" alt="Poner en portada"  title="Poner en portada"/>
                                           </a>
                                     </td>
                                {/if}
                                <td class='no_width' style="width:25px;" align="center">
                                       <a href="#" onClick="javascript:delete_article('{$articles[d]->id}','{$category}',0);" title="Eliminar">
                                           <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                                </td>
                        </tr>
                    </table>
                    {assign var=aux value=`$aux+1`}
                {/section}

              </div>
                    <p align="center" style="margin:20px;"> {$paginacion} </p>
           </div>
        </td>
   </tr>
</table>
		
				