<div class="content-row">
    
    <input type="checkbox" class="minput" name="selected_fld[]" value="{$item->id}" />
    
    {$item->title|clearslash}
    
    {if $category neq 'home'}
    
        <div class="inhome" style="display:inline;">
            {if $item->in_home == 1}
            <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}"  title="No sugerir en home"  alt="No sugerir en home">
                  <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/></a>
           {else}
                <a href="?id={$item->id}&amp;action=inhome_status&amp;status=1&amp;category={$category}" class="go_home" title="Sugerir en home" alt="Sugerir en home"></a>
            {/if}
        </div>
        
    {else}
    
        {$item->category_name}
        
    {/if}
    
    
    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$item->id}');" title="Editar">
            <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
    
    <a  onClick="javascript:confirmar_hemeroteca(this, '{$category}','{$item->id}') "  title="Archivar">
       <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
    
    {if $category neq 'home'}
        {if $item->frontpage == 1}
            <a href="?id={$item->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada">
                <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de portada" /></a>
        {else}
            <a href="?id={$item->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada">
                <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en portada" /></a>
        {/if}
        
        
        <a href="#" onClick="javascript:delete_article('{$item->id}','{$category}',0);" title="Eliminar">
            <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
    
    {else}
        <a href="?id={$item->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
    {/if}
    
</div>