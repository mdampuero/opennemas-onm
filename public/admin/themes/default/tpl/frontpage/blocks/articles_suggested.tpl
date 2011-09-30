<table class="adminlist">
    <thead>
        <tr>
            <th align="left" style="width:2%;"></th>
            <th align="left" style="width:40%;">{t}Title{/t}</th>
            <th style="width:5%;">{t}Views{/t}</th>
            <th style="width:10%;">{t}Created{/t}</th>
            <th style="width:10%;">{t}Author{/t}</th>
            <th style="width:10%;">{t}Last editor{/t}</th>
            <th style="width:10%;">{t}Category{/t}</th>
            <th style="width:6%;">{t}Actions{/t}</th>
        </tr>
    </thead>
    <tr><td colspan="13">
            <div id="articles-suggested" class="seccion" style="float:left;width:100%;">
                {assign var=aux value='100'}
                {section name=d loop=$suggestedArticles}

                    <table id='tabla{$aux}' name='tabla{$aux}' value="{$suggestedArticles[d]->id}" data="{$suggestedArticles[d]->content_type}" width="100%" class="tabla" style="text-align:center;padding:0px;padding-bottom:4px;">
                        <tr class="row1{schedule_class item=$suggestedArticles[d]}" style="cursor:pointer;">
                            <td align="left" style="width:2%;">
                                <input type="checkbox" class="minput" pos={$aux} id="selected_{$placeholder}_{$aux}" name="selected_fld[]" value="{$suggestedArticles[d]->id}"  style="cursor:pointer;" />
                            </td>
                             <td align="left" style="width:40%;"  onmouseout="UnTip()" onmouseover="Tip('<b>Creado:</b>{$suggestedArticles[d]->created}<br /><b>Vistos:</b>{$suggestedArticles[d]->views}<br /><b>Metadata:</b>{$suggestedArticles[d]->metadata}<br /><b>Publisher:</b>{$suggestedArticles[d]->authorName}<br /><b>Last Editor:</b>{$suggestedArticles[d]->lastEditorName}<br />{schedule_info item=$suggestedArticles[d]}', SHADOW, true, ABOVE, true, WIDTH, 300)" onClick="javascript:document.getElementById('selected_{$placeholder}_{$aux}').click();">

                                {is_clone item=$suggestedArticles[d]}{$suggestedArticles[d]->title|clearslash}
                            </td>
                            {if $category neq 'home'}
                                <td align="center">
                                    <div class="inhome" style="display:inline;">
                                        {if $suggestedArticles[d]->in_home == 1}
                                        <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                                              <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/>
                                              </a>
                                        {elseif $suggestedArticles[d]->in_home == 2}
                                            <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                                            <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" title="No sugerir en home" alt="No sugerir en home"/></a>
                                        {else}
                                            <a href="?id={$suggestedArticles[d]->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
                                        {/if}
                                    </div>
                                </td>
                            {else}
                                <td align="center" style="width:5%;" >
                                    {$suggestedArticles[d]->views}
                                </td>
                                <td align="center" style="width:10%;">
                                    {$suggestedArticles[d]->created}
                                </td>
                                <td align="center" style="width:10%;">
                                    {$suggestedArticles[d]->authorName}
                                </td>
                                <td align="center" style="width:10%;">
                                    {$suggestedArticles[d]->lastEditorName}
                                </td>
                                <td align="center" style="width:10%;">
                                    {$suggestedArticles[d]->catName}
                                </td>
                            {/if}
                            <td align="center" style="width:6%;">

                                <ul class="action-buttons">
                                    <li>
                                        <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}&action=read" title="Editar">
                                            <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" />
                                        </a>
                                    </li>
                                    <li>
                                        <a  onClick="javascript:confirmar_hemeroteca(this,'{$category}','{$suggestedArticles[d]->id}') "  title="Archivar">
                                           <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" />
                                        </a>
                                    </li>
                                    {if $category neq 'home'}
                                        <li>
                                            {if $suggestedArticles[d]->frontpage == 1}
                                            <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                                                <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" />
                                            </a>
                                            {else}
                                            <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                                                <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" />
                                            </a>
                                            {/if}
                                        </li>
                                        <li>
                                            <a href="#" onClick="javascript:delete_article('{$suggestedArticles[d]->id}'','{$category}',0);" title="Eliminar"><img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                                        </li>
                                    {else}
                                        <li>
                                            <a href="{$smarty.server.PHP_SELF}?id={$suggestedArticles[d]->id}'&action=inhome_status&status=0&category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                                        </li>
                                    {/if}
                                </ul>

                            </td>

                        </tr>
                    </table>

                    {assign var=aux value=$aux+1}
                {/section}

           </div>
        </td>
   </tr>
</table>
