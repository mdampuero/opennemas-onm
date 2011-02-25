<table width="100%" cellpadding="0" cellspacing="0" id="{$category->pk_content_category}">
    <tr {cycle values="class=row0,class=row1"}>
        <td style="padding: 0px 10px; height: 40px;">
             <b> {$category->title|clearslash|escape:"html"}</b>
        </td>
        <td style="padding: 0px 10px; height: 40px;width:100px;" align="center">
          {if $category->internal_category eq 3}
             <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
          {elseif $categorys->internal_category eq 5}
             <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
          {else}
              <img  style="width:20px;" src="{$params.IMAGE_DIR}advertisement.png" border="0" alt="Sección Global" />
          {/if}
        </td>
         <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {$num_contents.articles|default:0}
        </td>
        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {$num_contents.photos|default:0}
        </td>
        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {$num_contents.advertisements|default:0}
        </td>
        <td style="padding:10px;width:80px;" align="center">
            {if $category->inmenu==1}
                <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
            {else}
                <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
            {/if}
            {assign var=containers value=$category->inmenu}
        </td>
        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {if $category->internal_category != 0 && $category->internal_category != 2}
                <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$category->pk_content_category});" title="Modificar">
                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                </a>
            {/if}
        </td>
        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {if $category->internal_category != 0}
                <a href="#" onClick="if( confirm('¿Está usted seguro que desea vaciar la sección? \n ¡Atención! Eliminará todos sus contenidos') ) {ldelim} enviar(this, '_self', 'empty', {$category->pk_content_category}); {rdelim}" title="Vaciar">
                    <img src="{$params.IMAGE_DIR}removecomment.png" border="0" alt="vaciar" />
                </a>
            {/if}
        </td>
        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">
            {if $category->internal_category != 0 && $category->internal_category != 2}
                <a href="#" onClick="javascript:confirmar(this, {$category->pk_content_category});" title="Eliminar">
                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                </a>
            {/if}
        </td>
    </tr>
    <tr>
        <td colspan="9">
            {section name=su loop=$subcategorys}
                <table width="100%" cellpadding="0" cellspacing="0" id="{$subcategorys[su]->pk_content_category}" class="tabla">
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="padding: 0px 10px 0px 40px; height: 30px;  ">
                            <b>{$subcategorys[su]->title} </b>
                        </td>
                         <td align="center" style="padding:10px;width:80px;">
                              {if $subcategorys[su]->internal_category eq 3}
                                 <img style="width:20px;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
                              {elseif $subcategorys[su]->internal_category eq 5}
                                 <img  style="width:20px;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
                              {else}
                                  <img  style="width:20px;" src="{$params.IMAGE_DIR}advertisement.png" border="0" alt="Sección Global" />
                              {/if}
                        </td>
                        <td align="center" style="padding: 0px 10px; height: 30px;width:80px;">
                            {$num_sub_contents[su].articles|default:0}</a>
                        </td>
                        <td align="center" style="padding: 0px 10px; height: 30px;width:80px;">
                            {$num_sub_contents[su].photos|default:0}</a>
                        </td>
                        <td align="center" style="padding: 0px 10px; height: 30px;width:80px;">
                            {$num_sub_contents[su].advertisements|default:0}</a>
                        </td>
                        <td align="center" style="padding: 0px 10px; height: 30px;width:80px;">
                            {if $subcategory[su]->inmenu==1}
                                <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                            {else}
                                <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                            {/if}
                            {assign var=containers2 value=$subcategorys[su]->inmenu}
                        </td>
                        <td style="padding: 0px 10px; height: 30px;width:80px" align="center">
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[su]->pk_content_category});" title="Modificar">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                            </a>
                        </td>
                        <td style="padding: 0px 10px; height: 40px;width:80px;" align="center">

                            {if ($subcategorys[su]->internal_category==1) && ($num_sub_contents[su].articles!=0 || $num_sub_contents[su].photos!=0 || $num_sub_contents[su].advertisement!=0)}
                                <a href="#" onClick="if( confirm('¿Está usted seguro que desea vaciar la sección? ¡Atención! Eliminará todos sus contenidos') ) {ldelim} enviar(this, '_self', 'empty', {$subcategorys[su]->pk_content_category}); {rdelim}" title="Vaciar">
                                    <img src="{$params.IMAGE_DIR}removecomment.png" border="0" alt="vaciar" />
                                </a>
                            {/if}
                        </td>
                        <td style="padding: 0px 10px; height: 30px;width:80px;" align="center">
                            <a href="#" onClick="javascript:confirmar(this, {$subcategorys[su]->pk_content_category});" title="Eliminar">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                            </a>
                        </td>
                    </tr>
              </table>
          {/section}
        </td>
    </tr>
</table>
