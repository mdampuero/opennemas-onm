<table class="adminlist">
    <thead>
        <tr>
            <th align="center" style="width:15px;"></th>
            <th align="center">{t}Title{/t}</th>
            <th align="center" style="width:70px;">{t}Date{/t}</th>
            {if $other_category eq 'suggested'}
                <th align="center" style="width:60px;">{t}Section{/t}</th>
            {/if}
            <th align="center" style="width:80px;">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tr>
        <td colspan="13">
            <div id="other_articles_in_category" class="seccion" style="float:left;width:100%;">
                <div id="art" class="seccion" style="float:left;width:100%;">
                {assign var=aux value='100'}
                {section name=d loop=$articles}
                    <table id="tabla{$aux}" name="tabla{$aux}" width="100%" value="{$articles[d]->id}" class="tabla" style="text-align:center;padding:0px;">
                        <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                             <td align="left"  style="width:10px;" >
                                <input type="checkbox" class="minput" id="selected_fld_art_{$smarty.section.d.iteration}" name="no_selected_fld[]" value="{$articles[d]->id}" style="cursor:pointer;" >
                            </td>
                            <td style="padding:2px;" align="left" onClick="javascript:document.getElementById('selected_fld_art_{$smarty.section.d.iteration}').click();">
                                {is_clone item=$articles[d]}{$articles[d]->title|clearslash}
                            </td>
                            {if $other_category neq 'suggested'}
                            <td>
                                <div class="noinhome" style="display:none;">
                                    {if $articles[d]->in_home == 1}
                                        <a title="Publicada en home">
                                            <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicada en home" />
                                        </a>
                                    {else}
                                        {if $articles[d]->in_home == 2}
                                            <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" title="Publicar en home">
                                                <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="Publicar en home" />
                                            </a>
                                        {else}
                                            <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" title="Sugerir en home">
                                                <img class="inhome" src="{$params.IMAGE_DIR}home_no.png" border="0" alt="Sugerir en home" />
                                            </a>
                                        {/if}
                                    {/if}
                                 </div>
                            </td>
                            {/if}
                            {if $other_category eq 'suggested'}
                            <td style="width:60px;" align="center">
                               {$articles[d]->category_name}
                            </td>
                            {/if}
                            <td  style="width:80px; text-align:right; padding-right:10px;">
                                <ul class="action-buttons">
                                    <li>
                                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$articles[d]->id}');" title="Editar">
                                            <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
                                        </a>
                                    </li>
                                    <li>
                                        <a href="?id={$articles[d]->id}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="Archivar">
                                            <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" />
                                        </a>
                                    </li>
                                    <li>
                                        {if $other_category neq 'suggested'}
                                        <a href="?id={$articles[d]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage}" title="No en portada">
                                            <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="No en portada" />
                                        </a>
                                        {else}
                                        <a href="?id={$articles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" title="Poner en portada"  alt="Poner en portada">
                                             <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="Poner en portada"  title="Poner en portada"/>
                                        </a>
                                        {/if}
                                    </li>
                                    <li>
                                        <a href="#" onClick="javascript:delete_article('{$articles[d]->id}','{$category}',0);" title="Eliminar">
                                            <img height=16px  src="{$params.IMAGE_DIR}trash.png" border="0" />
                                        </a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                    {assign var=aux value=$aux+1}
                    {/section}
                </div>
                <div style="margin:20px;" class="pagination"> {$paginacion} </div>
           </div>
        </td>
   </tr>
</table>
