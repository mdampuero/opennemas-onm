<table class="tabla" width="640">
<thead>
    <tr>
        <th>Id.</th>
        <th>Categoría</th>
        <th width="50">Visitas</th>
        <th>&nbsp;</th>
    </tr>
</thead>

<tbody>
    {section name="cln" loop=$clones}
    <tr class="{cycle values="row0,row1"}">
        <td>
            {$clones[cln]->id}
        </td>
        <td>
            {$clones[cln]->category_title}
        </td>
        <td align="right">
            {$clones[cln]->views}&nbsp;
        </td>
        <td>
            &nbsp;&nbsp;&nbsp;&nbsp;
            
            <a href="article.php?action=read&category={$clones[cln]->category}&id={$clones[cln]->id}"
                title="Editar">
                <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
            
            <a href="index.php?go=article.php%3Faction%3Dread%26category%3D{$clones[cln]->category}%26id%3D{$clones[cln]->id}" target="_blank"
              title="Editar en otra ventana">
                <img src="{$params.IMAGE_DIR}editNewWindow.png" border="0" /></a>
            
            <a href="article.php?action=unlink&category={$clones[cln]->category}&id={$clones[cln]->id}"
              title="Desligar de artículo original">
                <img src="{$params.IMAGE_DIR}unlink.png" border="0" alt="Desligar de artículo original" /></a>    
            
            <a href="#" onclick="javascript:delete_article('{$clones[cln]->id}','{$category}', 0);" title="Eliminar">
                <img src="{$params.IMAGE_DIR}trash.png" border="0" alt="Eliminar" /></a>
            
        </td>
    </tr>
    {/section}
</tbody>
</table>