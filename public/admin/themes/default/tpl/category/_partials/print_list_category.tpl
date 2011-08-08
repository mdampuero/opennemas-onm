<table  id="{$category->pk_content_category}" style="width:100%">
    <tr {cycle values="class=row0,class=row1"} >
        <td style="width:26.5%;">
             <strong> {$category->title|clearslash|escape:"html"}</strong>
        </td>
        <td style="width:10%;" align="center">
             <strong> {$category->name}</strong>
        </td>
        <td style="width:10%;" align="center">
          {if $category->internal_category eq 7}
             <img style="width:15%;" src="{$params.IMAGE_DIR}album.png" border="0" title="Sección de Album" alt="Sección de Album" />
          {elseif $category->internal_category eq 9}
             <img  style="width:15%;" src="{$params.IMAGE_DIR}video.png" border="0" title="Sección de Videos"  alt="Sección de Videos" />
          {else}
              <img  style="width:15%;" src="{$params.IMAGE_DIR}advertisement.png" border="0" title="Sección Global"  alt="Sección Global" />
          {/if}
        </td>
         <td style="width:8%;" align="center">
            {$num_contents.articles|default:0}
        </td>
        <td style="width:8%;" align="center">
            {$num_contents.photos|default:0}
        </td>
        <td style="width:8%;" align="center">
            {$num_contents.advertisements|default:0}
        </td>
        <td style="width:8%;" align="center">
            {if $category->inmenu==1}
                <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                    <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
            {else}
                <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                    <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
            {/if}

        </td>
        <td style="width:8%;" align="center">
            <ul class="action-buttons">
                <li>
                {if $category->internal_category != 0 && $category->internal_category != 2}
                    <a href="{$smarty.server.PHP_SELF}?action=read&id={$category->pk_content_category}" title="Modificar">
                        <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                    </a>
                {/if}
                </li>
                <li>
                {if $category->internal_category != 0}
                    <a href="#" onClick="if( confirm('¿Está usted seguro que desea vaciar la sección? \n ¡Atención! Eliminará todos sus contenidos') ) {ldelim} enviar(this, '_self', 'empty', {$category->pk_content_category}); {rdelim}" title="Vaciar">
                        <img src="{$params.IMAGE_DIR}removecomment.png" border="0" alt="vaciar" />
                    </a>
                {/if}
                </li>
                <li>
                {if $category->internal_category != 0 && $category->internal_category != 2}
                    <a href="#" onClick="javascript:confirmar(this, {$category->pk_content_category});" title="Eliminar">
                        <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                    </a>
                {/if}
                </li>
            </ul>
        </td>
    </tr>
    {if count($subcategorys) >0}
    <tr>
        {section name=su loop=$subcategorys|default:array()}
            <tr {cycle values="class=row0,class=row1"}>
                <td style="padding-left: 40px;">
                    <b>{$subcategorys[su]->title} </b>
                </td>
                 <td style="width:10%;" align="center">
                    <b>{$subcategorys[su]->name} </b>
                </td>
                 <td align="center" style="width:10%;">
                      {if $subcategorys[su]->internal_category eq 7}
                         <img style="width:15%;" src="{$params.IMAGE_DIR}album.png" border="0" alt="Sección de Album" />
                      {elseif $subcategorys[su]->internal_category eq 9}
                         <img  style="width:15%;" src="{$params.IMAGE_DIR}video.png" border="0" alt="Sección de Videos" />
                      {else}
                          <img  style="width:15%;" src="{$params.IMAGE_DIR}advertisement.png" border="0" alt="Sección Global" />
                      {/if}
                </td>
                <td align="center" style="width:8%;">
                    {$num_sub_contents[su].articles|default:0}
                </td>
                <td align="center" style="width:8%;">
                    {$num_sub_contents[su].photos|default:0}
                </td>
                <td align="center" style="width:8%;">
                    {$num_sub_contents[su].advertisements|default:0}
                </td>
                <td align="center" style="width:8%;">
                    {if $subcategorys[su]->inmenu==1}
                        <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                    {else}
                        <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                    {/if}
                </td>

                <td style="width:8%;" align="center">
                    <ul class="action-buttons">
                        <li>
                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', {$subcategorys[su]->pk_content_category});" title="Modificar">
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                            </a>
                        </li>
                        <li>
                        {if ($subcategorys[su]->internal_category==1) && ($num_sub_contents[su].articles!=0 || $num_sub_contents[su].photos!=0 || $num_sub_contents[su].advertisement!=0)}
                            <a href="#" onClick="if( confirm('¿Está usted seguro que desea vaciar la sección? ¡Atención! Eliminará todos sus contenidos') ) {ldelim} enviar(this, '_self', 'empty', {$subcategorys[su]->pk_content_category}); {rdelim}" title="Vaciar">
                                <img src="{$params.IMAGE_DIR}removecomment.png" border="0" alt="vaciar" />
                            </a>
                        {/if}
                        </li>
                        <li>
                            <a href="#" onClick="javascript:confirmar(this, {$subcategorys[su]->pk_content_category});" title="Eliminar">
                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                            </a>
                        </li>
                    </ul>
                </td>
            </tr>
      {/section}
    </tr>
    {/if}
</table>
