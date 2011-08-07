{extends file="base/admin.tpl"}
{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilspoll.js"></script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra} :: {t}Creating a poll{/t}</h2></div>
            <ul class="old-button">
                <li>
                {if isset($poll->id)}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'update', '{$poll->id}', 'formulario');">
                {else}
                   <a href="#" onClick="javascript:sendFormValidate(this, '_self', 'create', '0', 'formulario');">
                {/if}
                        <img border="0" src="{$params.IMAGE_DIR}save.png" title="Guardar y salir" alt="Guardar y salir"><br />Guardar
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="sendFormValidate(this, '_self', 'validate', '{$poll->id}', 'formulario');" value="Validar" title="Validar">
                        <img border="0" src="{$params.IMAGE_DIR}save_and_continue.png" title="Guardar y continuar" alt="Guardar y continuar" ><br />Guardar y continuar
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" class="admin_add" onClick="enviar(this, '_self', 'list', 0);" onmouseover="return escape('<u>C</u>ancelar');" value="Cancelar" title="Cancelar">
                        <img border="0" src="{$params.IMAGE_DIR}previous.png" title="Cancelar" alt="Cancelar" ><br />{t}Go back{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">
        <table class="adminheading">
            <tr>
                <td></td>
            </tr>
        </table>
        <table class="adminform">
             <tbody>
                 <tr>
                     <td> </td><td > </td>
                     <td rowspan="6" valign="top" style="padding:4px;border:0px;">
                         <div align='center'>
                             <table style='background-color:#F5F5F5; padding:18px; width:69%;' cellpadding="8">
                                 <tr>
                                     <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                         <label for="title"> Disponible: </label>
                                     </td>
                                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                                         <select name="available" id="available" class="required">
                                            <option value="0" {if $poll->available eq 0} selected {/if}>No</option>
                                            <option value="1" {if $poll->available eq 1} selected {/if}>Si</option>
                                         </select>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                       <label for="title"> Favorito: </label>
                                     </td>
                                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                                         <select name="favorite" id="favorite" class="required">
                                            <option value="0" {if $poll->favorite eq 0} selected {/if}>No</option>
                                            <option value="1" {if $poll->favorite eq 1} selected {/if}>Si</option>
                                         </select>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                         <label for="title"> Visualización: </label>
                                     </td>
                                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                                          <select name="visualization" id="visualization" class="required">
                                              <option value="0" {if $poll->visualization eq 0} selected {/if}>Circular</option>
                                              <option value="1" {if $poll->visualization eq 1} selected {/if}>Barras</option>
                                          </select>
                                     </td>
                                 </tr>
                                 <tr>
                                     <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                         <label for="title"> Sección: </label>
                                     </td>
                                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                                         <select name="category" id="category"  >
                                            {section name=as loop=$allcategorys}
                                                <option value="{$allcategorys[as]->pk_content_category}" {if $video->category eq $allcategorys[as]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$allcategorys[as]->title}" >{$allcategorys[as]->title}</option>
                                                {section name=su loop=$subcat[as]}
                                                    <option value="{$subcat[as][su]->pk_content_category}" {if $video->category eq $subcat[as][su]->pk_content_category || $category eq $allcategorys[as]->pk_content_category}selected{/if} name="{$subcat[as][su]->title}">&nbsp;&nbsp;&nbsp;&nbsp;{$subcat[as][su]->title}</option>
                                                {/section}
                                            {/section}
                                         </select>
                                     </td>
                                 </tr>
                             </table>
                         </div>
                     </td>
                 </tr>
                 <tr>
                     <td valign="top" align="right" style="padding:4px;" width="30%">
                         <label for="title">T&iacute;tulo:</label>
                     </td>
                     <td style="padding:4px;" nowrap="nowrap" width="70%">
                         <input 	type="text" id="title" name="title" title="Titulo de la noticia"
                                 value="{$poll->title|clearslash|escape:"html"}" class="required" size="80" onChange="javascript:get_metadata(this.value);"  />
                     </td>
                 </tr>
                 <tr>
                     <td valign="top" align="right" style="padding:4px;" width="30%">
                         <label for="title">Subtitulo:</label>
                     </td>
                     <td style="padding:4px;" nowrap="nowrap" width="70%">
                         <input 	type="text" id="subtitle" name="subtitle" title="subTitulo de la noticia"
                                 value="{$poll->subtitle|clearslash}" class="required" size="80" />
                     </td>
                 </tr>
                 <tr>
                     <td valign="top" align="right" style="padding:4px;" width="30%">
                         <label for="title">Palabras Clave:</label>
                     </td>
                     <td style="padding:4px;" nowrap="nowrap" width="70%">
                         <input 	type="text" id="metadata" name="metadata" title="Titulo de la noticia"
                                 value="{$poll->metadata|clearslash}" class="required" size="80" />
                     </td>
                </tr>
                <tr>
                     <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
                             <label for="title"> Respuestas: </label>
                     </td>
                     <td valign="top" style="padding:4px;" nowrap="nowrap">

                         {assign var='num' value='0'}
                         {section name=i loop=$items}
                             <div id="item{$smarty.section.i.iteration}" class="marcoItem" style='display:inline;'>
                             <p style="font-weight: bold;" >Item #{$smarty.section.i.iteration}:  Votos:  {$items[i].votes} / {$poll->total_votes} </p>
                             Item: <input type="text" name="item[{$smarty.section.i.iteration}]" value="{$items[i].item}" id="item[{$smarty.section.i.iteration}]" size="45"/>
                              <input type="hidden" readonly name="votes[{$smarty.section.i.iteration}]" value="{$items[i].votes}" id="votes[{$smarty.section.i.iteration}]" size="8"/>
                             <a onclick="del_this_item('item{$smarty.section.i.iteration}')" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}del.png" border="0" />Eliminar item</a>
                             </div>
                             {assign var='num' value=$smarty.section.i.iteration}
                         {/section}

                     </td>
                 </tr>
                 <tr>
                     <td valign="top"  align="right" style="padding:4px;" nowrap="nowrap">
                     </td>
                     <td valign="top" style="padding:4px;" nowrap="nowrap">
                         <a onClick="add_item_poll({$num})" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}add.png" border="0" /> Añadir </a> &nbsp;
                         <a onclick="del_item_poll()" style="cursor:pointer;"><img src="{$params.IMAGE_DIR}del.png" border="0"   /> Eliminar</a>
                         <div id="items" name="items">
                         </div>
                     </td>
                 </tr>
             </tbody>
             </table>

         </div>


        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
{/block}
