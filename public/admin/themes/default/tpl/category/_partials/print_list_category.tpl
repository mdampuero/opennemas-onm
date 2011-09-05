<tr>
    <td>
        <a href="{$smarty.server.PHP_SELF}?action=read&id={$category->pk_content_category}" title="Modificar">
            {$category->title|clearslash|escape:"html"}
        </a>
    </td>
    <td>
        {$category->name}
    </td>
     <td class="center">
        {$num_contents.articles|default:0}
    </td>
    <td class="center">
        {$num_contents.photos|default:0}
    </td>
    <td class="center">
        {$num_contents.advertisements|default:0}
    </td>
    <td class="center">
        {if $category->inmenu==1}
            <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
        {else}
            <a href="?id={$category->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
        {/if}

    </td>
    <td class="center">
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
        <tr>
            <td style="padding-left: 20px;">
                &rArr;
                <a href="{$smarty.server.PHP_SELF}?action=read&id={$subcategorys[su]->pk_content_category}" title="Modificar">
                    <strong>{$subcategorys[su]->title}</strong>
                </a>
            </td>
            <td>
                {$subcategorys[su]->name}
            </td>
            <td class="center">
                {$num_sub_contents[su].articles|default:0}
            </td>
            <td class="center">
                {$num_sub_contents[su].photos|default:0}
            </td>
            <td class="center">
                {$num_sub_contents[su].advertisements|default:0}
            </td>
            <td class="center">
                {if $subcategorys[su]->inmenu==1}
                    <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=0" title="En menu">
                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                {else}
                    <a href="?id={$subcategorys[su]->pk_content_category}&amp;action=set_inmenu&amp;status=1" title="No en menu">
                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                {/if}
            </td>

            <td class="center">
                <ul class="action-buttons">
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=read&id={$subcategorys[su]->pk_content_category}" title="Modificar">
                            <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                        </a>
                    </li>
                    <li>
                    {if ($subcategorys[su]->internal_category==1) && ($num_sub_contents[su].articles!=0 || $num_sub_contents[su].photos!=0 || $num_sub_contents[su].advertisements!=0)}
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
