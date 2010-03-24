{include file="header.tpl"}

{* LISTADO ******************************************************************* *}
{if $smarty.request.action eq "list"}

    <div id="media_msg" style="float:right;width:300px;display:none;">   </div>
    {assign var=msg value=''}
    {if $smarty.request.resp eq 'SI'}
        {assign var=msg value='Se ha eliminado correctamente.'}
    {elseif $smarty.request.resp eq 'NO'}
        {assign var=msg value='No se ha eliminado, la seccion no esta vacia.'}
    {elseif $smarty.request.resp eq 'ya'}
        {assign var=msg value='No se ha podido crear, la seccion ya existe.'}
    {/if}
    {if !empty($msg)}
        <script type="text/javascript">
            {literal}
                showMsg({'warn':['{/literal}{$msg}. {literal}<br />  ']},'inline');
            {/literal}
        </script>
    {/if}

    <ul id="tabs">
        <li>
            <a href="category.php#listado">Listar secciones</a>
        </li>
        <li>
            <a href="#ordenar">Ordenar Secciones</a>
        </li>
    </ul>

    <div class="panel" id="listado" style="width:95%">

        {include file="botonera_up.tpl" type="list"}

        <table class="adminlist" id="tabla"  width="100%">
            <tr>
		<th  style="padding:10px" class="title">T&iacute;tulo</th>
		<th style="padding:10px;width:100px;" align="center">Nº Art&iacute;culos</th>
		<th style="padding:10px;width:100px;" align="center">Nº Fotos</th>
		<th style="padding:10px;width:100px;" align="center">Nº Publicidades</th>
		<th align="center" style="padding:10px;width:100px;">Ver En menu</th>
		<th align="center" style="padding:10px;width:100px;">Modificar</th>
		<th align="center" style="padding:10px;width:100px;">Eliminar</th>
            </tr>
            <tr>
            <td colspan="7">
                {assign var=containers value=1}
                <div id="ctes" class="seon" style="float:left;width:100%;border:1px solid gray;"> <br>
                    {section name=c loop=$categorys}
                        {if $containers neq $categorys[c]->inmenu} <hr> {/if}
                        <table width="100%" cellpadding=0 cellspacing=0  id="{$categorys[c]->pk_content_category}">
                            <tr {cycle values="class=row0,class=row1"}>
                                <td style="padding: 0px 10px; height: 40px;font-size: 11px;">
                                     <b> {$categorys[c]->title|clearslash|escape:"html"}</b>
                                </td>
                                <td style="padding: 0px 10px; height: 40px;font-size: 11px;width:100px;" align="center">
                                    {$num_contents[c].articles|default:0}</a>
                                </td>
                                <td style="padding: 0px 10px; height: 40px;font-size: 11px;width:100px;" align="center">
                                    {$num_contents[c].photos|default:0}</a>
                                </td>
                                <td style="padding: 0px 10px; height: 40px;font-size: 11px;width:100px;" align="center">
                                    {$num_contents[c].advertisements|default:0}</a>
                                </td>
                                <td style="padding:10px;font-size: 11px;width:100px;" align="center">
                                    {if $categorys[c]->inmenu==1}
                                            <a href="?id={$categorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                    {else}
                                            <a href="?id={$categorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                    {/if}
                                    {assign var=containers value=$categorys[c]->inmenu}
                                </td>
                                <td style="padding: 0px 10px; height: 40px;width:100px;" align="center">
                                    {if $categorys[c]->internal_category==1}
                                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$categorys[c]->pk_content_category});" title="Modificar">
                                            <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" />
                                        </a>
                                    {/if}
                                </td>
                                <td style="padding: 0px 10px; height: 40px;width:100px;" align="center">
                                    {if $categorys[c]->internal_category==1}
                                        <a href="#" onClick="javascript:confirmar(this, {$categorys[c]->pk_content_category});" title="Eliminar">
                                            <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" />
                                        </a>
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <td colspan=7>
                                    {section name=su loop=$subcategorys[c]}
                                        <table width="100%" cellpadding=0 cellspacing=0 id="{$subcategorys[c][su]->pk_content_category}" class="tabla">
                                            <tr {cycle values="class=row0,class=row1"}>
                                                <td style="padding: 0px 10px 0px 40px; height: 30px; font-size: 11px; ">
                                                    <b>{$subcategorys[c][su]->title} </b>
                                                </td>
                                                <td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:100px;">
                                                    {$num_sub_contents[c][su].articles|default:0}</a>
                                                </td>
                                                <td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:100px;">
                                                    {$num_sub_contents[c][su].photos|default:0}</a>
                                                </td>
                                                <td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:100px;">
                                                    {$num_sub_contents[c][su].advertisements|default:0}</a>
                                                </td>
                                                <td align="center" style="padding: 0px 10px; height: 30px;font-size: 11px;width:100px;">
                                                    {if $subcategorys[c][su]->inmenu==1}
                                                        <a href="?id={$subcategorys[c][su]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                                                            <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                                    {else}
                                                        <a href="?id={$subcategorys[c][su]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                                                            <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                                    {/if}
                                                    {assign var=containers2 value=$subcategorys[c][su]->inmenu}
                                                </td>
                                                <td style="padding: 0px 10px; height: 30px;width:100px" align="center">
                                                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[c][su]->pk_content_category});" title="Modificar">
                                                        <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" />
                                                    </a>
                                                </td>
                                                <td style="padding: 0px 10px; height: 30px;width:100px;" align="center">
                                                    <a href="#" onClick="javascript:confirmar(this, {$subcategorys[c][su]->pk_content_category});" title="Eliminar">
                                                        <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" />
                                                    </a>
                                                </td>
                                            </tr>
                                      </table>
                                  {/section}
                                </td>
                            </tr>
                        </table>
                    {sectionelse}
                        <h2><b>Ning&uacute;na secci&oacute;n guardada</b></h2>
                    {/section}
                </div>
            </td></tr>
            {if count($categorys) gt 0}
                <tr>
                    <td colspan="5" style="padding:10px;font-size: 12px;" align="center"><br>{$paginacion->links}<br></td>
                </tr>
            {/if}
        </table>
    </div>

{* FORMULARIO PARA ORDENAR NOTICIAS ************************************** *}

    <div class="panel" id="ordenar" style="width:95%">

        {include file="botonera_up.tpl" type="order"}

        <table class="adminlist" id="tabla"  width="99%" cellpadding=0 cellspacing=0 >
            <tr>
                <th width="30%" class="title">T&iacute;tulo</th>
                <th width="30%" align="center">Nombre interno</th>
                <th align="center" width="10%">Ver En menu</th>
                <th align="center" width="15%">Modificar</th>
                <th align="center" width="25%">Eliminar</th>

            </tr>
            <tr>
                <td colspan="5">
                    <div id="cates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br />
                        {section name=c loop=$ordercategorys}
                            {if $ordercategorys[c]->pk_content_category neq "20"}
                                <table width="100%"  id="{$ordercategorys[c]->pk_content_category}" class="tabla" cellpadding=0 cellspacing=0 >
                                    <tr {cycle values="class=row0,class=row1"} style="cursor:pointer;border:0px; padding:0px;margin:0px;">
                                        <td style="padding:10px;font-size: 11px;width:30%;">
                                            {$ordercategorys[c]->title}
                                        </td>
                                        <td align="center" style="padding:10px;font-size: 11px;width:30%;">
                                            {$ordercategorys[c]->name|clearslash}</a>
                                        </td>

                                        <td align="center" style="padding:10px;font-size: 11px;width:10%;">
                                            {if $ordercategorys[c]->inmenu==1}
                                                <a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_g.png" border="0" alt="Publicado" /></a>
                                            {else}
                                                <a href="?id={$ordercategorys[c]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                                                    <img src="{php}echo($this->image_dir);{/php}publish_r.png" border="0" alt="Pendiente" /></a>
                                            {/if}
                                        </td>
                                        <td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
                                            {if $ordercategorys[c]->internal_category==1}
                                                <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$ordercategorys[c]->pk_content_category});" title="Modificar">
                                                    <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" />
                                                </a>
                                            {/if}
                                        </td>
                                        <td style="padding: 0px 10px; height: 40px;width:15%;" align="center">
                                            {if $ordercategorys[c]->internal_category==1}
                                                <a href="#" onClick="javascript:confirmar(this, {$ordercategorys[c]->pk_content_category});" title="Eliminar">
                                                    <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" />
                                                </a>
                                                {else} &nbsp;
                                            {/if}
                                        </td>
                                    </tr>
                                </table>
                            {/if}
                        {sectionelse}
                            <h2><b>Ning&uacute;na secci&oacute;n guardada</b></h2>
                        {/section}
                    </div>
                </td>
            </tr>
        </table>
        {literal}
            <script type="text/javascript">
                // <![CDATA[
                    Sortable.create('cates',{
                        tag:'table',
                        dropOnEmpty: true,
                        containment:["cates"],
                        constraint:false});
                // ]]>
            </script>
        {/literal}
    </div>

{/if}
 
{* FORMULARIO PARA ENGADIR ************************************** *}

{if isset($smarty.request.action) && ($smarty.request.action eq "new" || $smarty.request.action eq "read")}

    {include file="botonera_up.tpl"}

    <div id="warnings-validation"></div>

    <table style="margin-left:30px;" border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo">
        <tbody>
            <tr>
                <td valign="top" style="padding:4px;text-align:left; width:100px;">
                    <label for="title">T&iacute;tulo:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap"  colspan="2">
                    <input type="text" id="title" name="title" title="Título" value="{$category->title|clearslash}"
                        class="required" size="100" />
                </td>
            </tr>
            {if !empty($category->name)}
                <tr>
                    <td valign="top"  style="padding:4px;text-align:left; width:100px;">
                            <label for="title">Nombre interno:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap"   colspan="2">
                    <input type="text" id="name" name="name" title="carpeta categoria" readonly
                                    value="{$category->name|clearslash}" class="required" size="100" />
                    </td>
                </tr>
            {/if}
            <tr>
                <td valign="top" style="padding:4px;text-align:left; width:100px;">
                    <label for="title">Subsecci&oacute;n de:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                    <select name="subcategory" class="required" size="12">
                        <option value="0" {if empty($category->fk_content_category) || $category->fk_content_category eq '0'}selected{/if}> </option>
                        {section name=as loop=$allcategorys}
                            <option value="{$allcategorys[as]->pk_content_category}" {if $category->fk_content_category eq $allcategorys[as]->pk_content_category}selected{/if}>{$allcategorys[as]->title}</option>
                        {/section}
                    </select>
                </td>
                {* TODO: gestionar dinamicamente, con tabla content_type *}
                <td valign="top" style="padding:10px;text-align:left; width:340px;">
                    <h3>Contenido al que pertenece: </h3>
                     <div class="utilities-conf" style="width:60%;">
                        <table style="padding:4px; margin-left:10px;">
                            <tr>
                                <td  style="padding:4px;"> Global:</td>
                                <td>
                                    <input type="checkbox" id="internal_category" name="internal_category"  value="1"
                                    {if empty($category->fk_content_category) || $category->internal_category eq 1} checked="checked"{/if}>
                                </td>
                                 <td  style="padding:4px;"> </td>
                                <td  style="padding:4px;"> Álbumes:</td>
                                <td> <input type="checkbox" id="internal_category" name="internal_category"  value="3"
                                    {if $category->internal_category eq 3} checked="checked"{/if}>
                                </td>
                            </tr>
                             <tr>
                                <td  style="padding:4px;"> Vídeos:</td>
                                <td> <input type="checkbox" id="internal_category" name="internal_category"  value="5"
                                    {if $category->internal_category eq 5} checked="checked"{/if}>
                                </td>
                               <td  style="padding:4px;"> </td>
                                <td style="padding:4px;" > Kiosco: </td>
                                <td> <input type="checkbox" id="internal_category" name="internal_category"  value="4"
                                    {if $category->internal_category eq 4} checked="checked"{/if}>
                                </td>
                            </tr>
                        </table>
                     </div>
                </td>
            </tr>
            <tr>
                <td valign="top" style="padding:4px;text-align:left; width:100px;">
                    <label for="inmenu">Ver en menú:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" colspan="2" >
                    <input type="checkbox" id="inmenu" name="inmenu"  value="1" 
                        {if empty($category->fk_content_category) || $category->inmenu eq 1} checked="checked"{/if}>* Activado, ver en el menú de portada.
                </td>
            </tr>             
             <tr>
                <td valign="top" style="padding:4px;text-align:left; width:100px;">
                    <label for="inmenu">Logo en Portada:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" >
                    <input type="file" id="logo_path" name="logo_path"  />
                </td>
                 <td style="padding:4px;" nowrap="nowrap" rowspan="2" >
                     {if !empty($category->logo_path)}<img src="../media/sections/{$category->logo_path}" >{/if}
                 </td>
            </tr>
            <tr>
                <td valign="top" style="padding:4px;text-align:left; width:100px;">
                    <label for="inmenu">Color:</label>
                </td>
                <td style="padding:4px;" nowrap="nowrap" colspan="2" >
                    {literal}<script>initPicker('color','{/literal}{$category->color}{literal}',24);</script>{/literal}
                   
                </td>
            </tr>

            {if !empty($subcategorys)}
                <tr>
                    <td valign="top" style="padding:4px;text-align:left; width:100px;">
                        <label>Subsecciones:</label>
                    </td>
                    <td style="padding:4px;" nowrap="nowrap" colspan="2"> <br />
                        <table border="0" class="adminlist" id="cates">
                            <tr>

                                <th class="title"  width="25%">T&iacute;tulo</th>
                                <th  width="25%">Nombre interno</th>
                                <th align="center">En menu</th>
                                <th align="center" style="padding:10px;width:20%;">Modificar</th>
                                <th align="center" style="padding:10px;width:20%;">Eliminar</th>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <div id="subcates" class="seccion" style="float:left;width:100%;border:1px solid gray;"> <br>
                                        {section name=s loop=$subcategorys}
                                            <table width="100%" class="tabla" style="cursor:pointer;" id="{$subcategorys[s]->pk_content_category}">
                                                <tr {cycle values="class=row0,class=row1"}>
                                                    <td style="padding:10px;font-size: 11px;width:25%;">
                                                         {$subcategorys[s]->title}</a>
                                                    </td>
                                                    <td style="padding:10px;font-size: 11px;width:25%;">
                                                         {$subcategorys[s]->name}</a>
                                                    </td>

                                                    <td style="padding:10px;font-size: 11px;width:10%;"  align="center">
                                                        {if $subcategorys[s]->inmenu==1} Si {else}No {/if}
                                                    </td>
                                                    <td style="padding:10px;width:20%;" align="center">
                                                        <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[s]->pk_content_category});" title="Modificar">
                                                            <img src="{php}echo($this->image_dir);{/php}edit.png" border="0" />
                                                        </a>
                                                    </td>
                                                    <td style="padding:10px;width:20%;" align="center">
                                                        <a href="#" onClick="javascript:confirmar(this, {$subcategorys[s]->pk_content_category});" title="Eliminar">
                                                            <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" />
                                                        </a>
                                                    </td>
                                                </tr>
                                            </table>
					{/section}	
                                    </div>
                                </td>
                            </tr>
                        </table>
                        {literal}
                            <script type="text/javascript">
                            // <![CDATA[
                                Sortable.create('subcates',{
                                    tag:'table',
                                    dropOnEmpty: true,
                                    containment:["subcates"],
                                    constraint:false});
                            // ]]>
                            </script>
                        {/literal}
                    </td>
                </tr>
            {/if}
        </tbody>
    </table>

{/if}

{include file="footer.tpl"}