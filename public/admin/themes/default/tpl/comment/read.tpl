{extends file="base/admin.tpl"}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar clearfix">
            <div class="wrapper-content">
                <div class="title"><h2>{t}Comment Manager{/t} :: {t}Editing comment{/t}</h2></div>
                <ul class="old-button">
                    <li>
                        <a href="#" class="admin_add" onClick="enviar(this, '_self', 'update', '{$comment->id}');">
                            <img border="0" src="{$params.IMAGE_DIR}save.png" ="Guardar y salir" alt="Guardar y salir" ><br />Guardar y salir
                        </a>
                    </li>
                    <li>
                        <a href="#" class="admin_add" onClick="confirmar(this, '{$comment->id}');">
                            <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
                        </a>
                    </li>
                    <li>
                        {if $comment->content_status == 1}
                            <a href="?id={$comment->id}&amp;action=change_status&amp;status=0&amp;category={$comment->category}" title="Publicar">
                                <img src="{$params.IMAGE_DIR}publish_no.gif" border="0" alt="Publicado" /><br />Despublicar
                            </a>
                        {else}
                            <a href="?id={$comment->id}&amp;action=change_status&amp;status=1&amp;category={$comment->category}" title="Despublicar">
                                <img src="{$params.IMAGE_DIR}publish.gif" border="0" alt="Pendiente" /><br />Publicar
                            </a>
                        {/if}
                    </li>
                    <li>
                        <a href="#" class="admin_add" rel="iframe" onmouseover="return escape('<u>V</u>er Noticia');" onclick="preview(this, '{$article->category}','{$article->subcategory}','{$article->id}');">
                            <img border="0" src="{$params.IMAGE_DIR}preview.png" title="Ver Noticia" alt="Ver Noticia" ><br />Ver noticia
                        </a>
                    </li>
                    <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=list" value="{t}Go back{/t}" title="{t}Go back{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}previous.png" title="{t}Go back{/t}" alt="{t}Go back{/t}" ><br />{t}Go back{/t}
                        </a>
                    </li>
                </ul>
            </div>
        </div>


    <div class="wrapper-content">
        <table class="adminheading">
            <th>
                <td></td>
            </th>
        </table>
        <table class="adminform">
         <tbody>
         <tr>
             <td valign="top" align="right" style="padding:4px;" width="30%">
                 <label for="title">T&iacute;tulo:</label>
             </td>
             <td style="padding:4px;" nowrap="nowrap" width="70%">
                 <input type="text" id="title" name="title" title="Título de la noticia" onkeyup="countWords(this,document.getElementById('counter_title'))"
                     value="{$comment->title|clearslash|escape:"html"}" class="required" size="100" />
                 <input type="hidden" id="fk_content" name="fk_content" title="pk_article"
                     value="{$comment->fk_content}" />
             </td>

             <td rowspan="5" valign="top">
                     <table style='background-color:#F5F5F5; padding:8px;' cellpadding="8">
                      <tr>
                             <td valign="top" align="right" style="padding:4px;">
                                 <label for="title">Fecha:</label>
                             </td>
                             <td style="padding:4px;" nowrap="nowrap" >
                                 <input type="text" id="date" name="date" title="author"
                                 value="{$comment->created}" class="required" size="20" readonly /></td>
                         </tr>
                       <tr>
                             <td valign="top" align="right" style="padding:4px;" nowrap="nowrap">
                                 <label for="title"> Publicado: </label>
                             </td>
                                 <td valign="top" style="padding:4px;" nowrap="nowrap">
                                     <select name="content_status" id="content_status" class="required">
                                         <option value="1" {if $comment->content_status eq 1} selected {/if}>Si</option>
                                         <option value="0" {if $comment->content_status eq 0} selected {/if}>No</option>
                                    </select>
                         </td>
                         </tr>
                         <tr>
                             <td valign="top" align="right" style="padding:4px;" nowrap>
                                 <label for="title">IP:</label>
                             </td>
                             <td style="padding:4px;" nowrap="nowrap" >
                                 <input type="text" id="ip" name="ip" title="author"
                                 value="{$comment->ip}" class="required" size="20" readonly /></td>
                         </tr>
                         <tr>
                             <td valign="top" align="right" style="padding:4px;" nowrap>
                                 <label for="title">Nº Palabras t&iacute;tulo:</label>
                             </td>
                             <td style="padding:4px;" nowrap="nowrap" >
                                 <input type="text" id="counter_title" name="counter_title" title="counter_title"
                                     value="0" class="required" size="5" onkeyup="countWords(document.getElementById('title'),this)"/>
                             </td>
                         </tr>

                         <tr>
                             <td valign="top" align="right" style="padding:4px;" nowrap>
                                 <label for="title">Nº Palabras cuerpo:</label>
                             </td>
                             <td style="padding:4px;" nowrap="nowrap" >
                                 <input type="text" id="counter_body" name="counter_body" title="counter_body"
                                     value="0" class="required" size="5"  onkeyup="counttiny(document.getElementById('counter_body'));"/>
                             </td>
                         </tr>
                         </table>
             </td>
         </tr>

         <tr>
             <td valign="top" align="right" style="padding:4px;" width="30%">
                 <label for="title">Autor:</label>
             </td>
             <td style="padding:4px;" nowrap="nowrap" width="70%">
                 <input type="text" id="author" name="author" title="author"
                     value="{$comment->author|clearslash}" class="required" size="40" />
                     <label for="title"> Email:</label><input type="text" id="email" name="email" title="email"
                     value="{$comment->email|clearslash}" class="required" size="40" />
             </td>
         </tr>

         <tr>
             <td valign="top" align="right" style="padding:4px;" width="30%">
                 <label for="body">Cuerpo:</label>
             </td>
             <td style="padding:4px;" nowrap="nowrap" width="70%">
                 <textarea name="body" id="body"
                     title="comment" style="width:96%; height:20em;">{$comment->body|clearslash}</textarea>
             </td>
         </tr>
         </tbody>
         </table>

         </div>

             <div id="article-info" style="display:none;">
              <table border="0" cellpadding="3" cellspacing="0">
                     <tbody>
                     <tr><td><label for="title">Comentario: </label></td>
                     <td>
                         <h2> <a style="cursor:pointer;"  onClick="new Effect.BlindDown('edicion-contenido'); new Effect.BlindUp('article-info'); return false;">
                         {$comment->title|clearslash}</a></h2>
                     </td></tr>
                     <tr><td></td>
                       <td>
                          <table border="0" width="60%" style='background-color:#F5F5F5; padding:8px;' cellpadding="8">
                         <tbody>
                         <tr><td>
                             <h3>{$article->subtitle|clearslash}</h3>
                              <h3 > {$article->agency|clearslash} - {$article->created|date_format:"%d/%m/%y "}</h3>

                             <h2>{$article->title|clearslash}</h2>
                              <p>  <span style="float:left;"><img src="{$photo1->path_file}/{$photo1->name}" id="change1" name="{$article->img1}" border="0" width="180px" /></span>
                              {$article->summary|clearslash}
                                 </p>

                          </td></tr>
                          <tr><td>
                                 <p>
                                      <span style="float:right;">
                                       <img src="{$photo2->path_file}/{$photo2->name}" id="change1" name="{$article->img2}" border="0" width="300px" /></span>
                                     {$article->body|clearslash}
                                 </p>


                             </td>
                         </tr>
                         </tbody>
                         </table>
                     </td>
                 </tr>
                 </tbody>
                 </table>
             </div>

             <script>
                 countWords(document.getElementById('title'), document.getElementById('counter_title'));
                 countWords(document.getElementById('body'), document.getElementById('counter_body'));
             </script>

         <script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
         <script type="text/javascript" language="javascript">
             tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
         </script>

         <script type="text/javascript" language="javascript">
             OpenNeMas.tinyMceConfig.advanced.elements = "body";
             tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
         </script>


        {dialogo script="print"}

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
